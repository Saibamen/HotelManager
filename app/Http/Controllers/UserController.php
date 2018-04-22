<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserTableService;
use Illuminate\Support\Facades\Auth;

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
        ];

        return view('list', $viewData);
    }

    public function delete($objectId)
    {
        $object = User::find($objectId);
        $data = ['class' => 'alert-success', 'message' => trans('general.deleted')];

        // TODO: isAdmin()
        if (!$object || $object->id === 1 || $object->id = Auth::user()->id /*|| $object->isAdmin()*/) {
            $data = ['class' => 'alert-danger', 'message' => trans('general.cannot_delete_object')];
        } else {
            $object->delete();
        }

        return response()->json($data);
    }
}
