<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UserAddRequest;
use App\Models\User;
use App\Services\UserTableService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $userTableService;

    public function __construct(UserTableService $userTableService)
    {
        $this->userTableService = $userTableService;
    }

    public function index()
    {
        $title = trans('general.users');

        $dataset = User::select('id', 'name', 'email', 'created_at')
            ->paginate($this->getItemsPerPage());

        if ($dataset->isEmpty()) {
            $this->addFlashMessage(trans('general.no_users_in_database'), 'alert-danger');
        }

        $viewData = [
            'columns'       => $this->userTableService->getColumns(),
            'dataset'       => $dataset,
            'routeName'     => $this->userTableService->getRouteName(),
            'title'         => $title,
            'disableEdit'   => true,
        ];

        return view('list', $viewData);
    }

    public function showAddForm()
    {
        $dataset = new User();
        $title = trans('navigation.add_user');

        $viewData = [
            'dataset'     => $dataset,
            'fields'      => $this->getFields(),
            'title'       => $title,
            'submitRoute' => route($this->userTableService->getRouteName().'.postadd'),
            'routeName'   => $this->userTableService->getRouteName(),
        ];

        return view('addedit', $viewData);
    }

    public function postAdd(UserAddRequest $request)
    {
        $object = new User();

        $request->merge(['password' => Hash::make($request->password)]);
        $object->fill($request->all());
        $object->save();

        return redirect()->route($this->userTableService->getRouteName().'.index')
            ->with([
                'message'     => trans('general.saved'),
                'alert-class' => 'alert-success',
            ]);
    }

    public function delete($objectId)
    {
        $object = User::find($objectId);
        $data = ['class' => 'alert-success', 'message' => trans('general.deleted')];

        if (!$object || $object->id === 1 || $object->id = Auth::user()->id) {
            $data = ['class' => 'alert-danger', 'message' => trans('general.cannot_delete_object')];
        } else {
            $object->delete();
        }

        return response()->json($data);
    }

    public function changePassword()
    {
        return view('auth.passwords.change');
    }

    public function postChangePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route($this->userTableService->getRouteName().'.change_password')
            ->with([
                'message'     => trans('general.saved'),
                'alert-class' => 'alert-success',
            ]);
    }

    public function getFields()
    {
        return [
            [
                'id'    => 'name',
                'title' => trans('general.name'),
                'value' => function (User $data) {
                    return $data->name;
                },
                'optional' => [
                    'required' => 'required',
                ],
            ],
            [
                'id'    => 'email',
                'title' => trans('auth.email'),
                'value' => function (User $data) {
                    return $data->email;
                },
                'type'     => 'email',
                'optional' => [
                    'required' => 'required',
                ],
            ],
            [
                'id'       => 'password',
                'title'    => trans('auth.password'),
                'type'     => 'password',
                'optional' => [
                    'required' => 'required',
                ],
            ],
            [
                'id'       => 'password_confirmation',
                'title'    => trans('auth.password_confirmation'),
                'type'     => 'password',
                'optional' => [
                    'required'    => 'required',
                ],
            ],
            [
                'id'    => 'is_admin',
                'title' => trans('general.administrator'),
                'value' => function () {
                    return true;
                },
                'type'     => 'checkbox',
            ],
        ];
    }
}
