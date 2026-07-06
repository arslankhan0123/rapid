<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Laracasts\Flash\Flash;

/**
 * Class UserController
 */
class UserController extends AppBaseController
{
    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    /**
     * @param  ChangePasswordRequest  $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $input = $request->all();

        try {
            $user = $this->userRepository->changePassword($input);

            return $this->sendSuccess(__('messages.user.password_updated_successfully'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 422);
        }
    }

    /**
     * @return Application|Factory|View|RedirectResponse
     */
    public function editProfile()
    {
        try {
            $user = Auth::user();

            return view('clients.user_profile.edit', compact('user'));
        } catch (Exception $e) {
            return Redirect::back()->withErrors([$e->getMessage()]);
        }
    }

    /**
     * @param  UpdateUserProfileRequest  $request
     * @return Application|Redirector|RedirectResponse
     */
    public function updateProfile(UpdateUserProfileRequest $request)
    {
        try {
            $user = Auth::user();

            if (empty($user)) {
                Flash::error('User not found');

                return redirect(route('clients.dashboard'));
            }

            $input = $request->all();
            $this->userRepository->profileUpdate($input);

            Flash::success(__('messages.user.profile_updated_successfully'));

            return redirect(route('clients.dashboard'));
        } catch (Exception $e) {
            return Redirect::back()->withErrors([$e->getMessage()])->withInput($request->all());
        }
    }

    /**
     * @param  Request  $request
     * @return mixed
     */
    public function changeLanguage(Request $request)
    {
        $defaultLanguage = $request->get('default_language');

        try {
            $user = Auth::user();
            $user->update(['default_language' => $defaultLanguage]);

            return $this->sendSuccess(__('messages.user.language_updated_successfully'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 422);
        }
    }
}
