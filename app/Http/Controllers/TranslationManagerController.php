<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\User;
use App\Queries\TranslatorManagerDataTable;
use App\Repositories\TranslationManagerRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Laracasts\Flash\Flash;
use Yajra\DataTables\DataTables;

class TranslationManagerController extends AppBaseController
{
    /**
     * @var TranslationManagerRepository
     */
    private $translateManagerRepo;

    public function __construct(TranslationManagerRepository $translateManagerRepo)
    {
        $this->translateManagerRepo = $translateManagerRepo;
    }

    /**
     * @param Request $request
     * @throws Exception
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new TranslatorManagerDataTable())->get())->make('true');
        }

        return view('translation_manager.index');
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();
        $language = $this->translateManagerRepo->create($input);
        $this->translateManagerRepo->store($input);
        Artisan::call('lang:js');

        return $this->sendSuccess(__('messages.translator_manager.language_added_successfully'));
    }

    /**
     * @param  Language  $language
     * @return mixed
     */
    public function edit(Language $language): mixed
    {
        return $this->sendResponse($language, 'Language data retrieved successfully.');
    }
    
    public function update(Language $language, Request $request)
    {
        $input = $request->all();
        $this->translateManagerRepo->updateLanguage($input, $language);

        return $this->sendSuccess(__('messages.translator_manager.language_updated_successfully'));
    }

    /**
     * @param  Language  $language
     * @param  Request  $request
     * @return Application|Factory|View|RedirectResponse
     */
    public function showTranslation(Language $language, Request $request)
    {
        $selectedLang = $request->get('name', $language->name);
        $selectedFile = $request->get('file', 'messages.php');
        $langExists = $this->translateManagerRepo->checkLanguageExistOrNot($selectedLang);

        if (! $langExists) {
            return redirect()->back()->withErrors($selectedLang.' language not found.');
        }

        $fileExists = $this->translateManagerRepo->checkFileExistOrNot($selectedLang, $selectedFile);

        if (! $fileExists) {
            return redirect()->back()->withErrors($selectedFile.' file not found.');
        }

        $oldLang = app()->getLocale();
        $data = $this->translateManagerRepo->getSubDirectoryFiles($selectedLang, $selectedFile);
        $data['id'] = $language->id;
        app()->setLocale($oldLang);

        return view('translation_manager.edit_translator_language',
            compact('selectedLang', 'selectedFile'))->with($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Language  $language
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Language $language): JsonResponse
    {
        if ($language->is_default) {
            return $this->sendError('Default Language can\'t be deleted.');
        }

        $usesLang = User::whereIsEnable(true)->pluck('default_language')->toArray();

        if (in_array($language->name, $usesLang)) {
            return $this->sendError('Uses Language can\'t be deleted.');
        }

        $path = base_path().'/lang/'.$language->name;

        if (File::exists($path)) {
            File::deleteDirectory($path);
        }

        $language->delete();
        Artisan::call('lang:js');

        return $this->sendSuccess('Language deleted successfully.');
    }

    /**
     * @param  Language  $language
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function updateTranslation(Language $language, Request $request)
    {
        $lName = $language->name;
        $fileName = $request->get('file_name');
        $fileExists = $this->translateManagerRepo->checkFileExistOrNot($lName, $fileName);

        if (! $fileExists) {
            return redirect()->back()->withErrors('File not found.');
        }

        if (! empty($lName)) {
            $result = $request->except(['_token', 'translate_language', 'file_name']);
            File::put(base_path('lang/'.$lName.'/'.$fileName), '<?php return '.var_export($result, true).'?>');
        }

        Artisan::call('lang:js');

        Flash::success(__('messages.translator_manager.language_updated_successfully'));

        return redirect()->route('language.translation', $language->id);
    }
}
