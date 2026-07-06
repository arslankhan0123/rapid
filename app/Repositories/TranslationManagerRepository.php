<?php

namespace App\Repositories;

use App\Models\Language;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class TranslationManagerRepository
 */
class TranslationManagerRepository extends BaseRepository
{
    /**
     * @return string[]
     */
    public function getFieldsSearchable(): array
    {
        return ['name'];
    }

    /**
     * @return string
     */
    public function model(): string
    {
        return Language::class;
    }

    /**
     * @param $input
     * @return bool
     */
    public function store($input)
    {
        $allLanguagesArr = [];
        $languages = File::directories(base_path().'/lang');
        foreach ($languages as $language) {
            $allLanguagesArr[] = substr($language, -2);
        }

        if (in_array(strtolower($input['name']), $allLanguagesArr)) {
            throw new UnprocessableEntityHttpException($input['name'].' language already exists.');
        }

        try {
            if (! empty($input['name'])) {
                //Make directory in lang folder
                File::makeDirectory(base_path().'/lang'.'/'.$input['name']);

                //Copy all en folder files to new folder.
                $filesInFolder = File::files(App::langPath().'/en');
                foreach ($filesInFolder as $path) {
                    $file = basename($path);
                    File::copy(App::langPath().'/en/'.$file, App::langPath().'/'.$input['name'].'/'.$file);
                }
            }

            return true;
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @param $selectedLang
     * @param $selectedFile
     * @return array
     */
    public function getSubDirectoryFiles($selectedLang, $selectedFile): array
    {
        $data['allFiles'] = [];
        try {
            $files = File::allFiles(App::langPath().'/'.$selectedLang.'/');
            foreach ($files as $file) {
                $data['allFiles'][basename($file)] = ucfirst(basename($file));
            }

            $data['languages'] = File::directories(base_path().'/lang');
            $data['allLanguagesArr'] = [];
            foreach ($data['languages'] as $language) {
                $lName = substr($language, -2);
                $data['allLanguagesArr'][$lName] = strtoupper(substr($language, -2));
                app()->setLocale(substr($selectedLang, -2));
                $data['languages'] = trans(pathinfo($selectedFile, PATHINFO_FILENAME));
            }

            return $data;
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @param $selectedLang
     * @param $selectedFile
     * @return bool
     */
    public function checkFileExistOrNot($selectedLang, $selectedFile): bool
    {
        $fileExists = true;
        $data['allFiles'] = [];
        try {
            $files = File::allFiles(App::langPath().'/'.$selectedLang.'/');
            foreach ($files as $file) {
                $data['allFiles'][] = ucfirst(basename($file));
            }

            if (! in_array(ucfirst($selectedFile), $data['allFiles'])) {
                $fileExists = false;
            }

            return $fileExists;
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @param $selectedLang
     * @return bool
     */
    public function checkLanguageExistOrNot($selectedLang): bool
    {
        $langExists = true;
        $allLanguagesArr = [];
        try {
            $languages = File::directories(base_path().'/lang');
            foreach ($languages as $language) {
                $allLanguagesArr[] = substr($language, -2);
            }

            if (! in_array($selectedLang, $allLanguagesArr)) {
                $langExists = false;
            }

            return $langExists;
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @param $input
     * @param $language
     * @return bool
     */
    public function updateLanguage($input, $language): bool
    {
        try {
            DB::begintransaction();

            $oldLang = $language->name;
            $language->update($input);

            if ($language->name != $oldLang) {
                $ifExist = $this->checkLanguageExistOrNot($language->name);
                if ($ifExist) {
                    throw new UnprocessableEntityHttpException($language->name.' '.__('messages.placeholder.lang_already_exists'));
                }

                File::move(App::langPath().'/'.$oldLang.'/', App::langPath().'/'.$language->name);
            }

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }
}
