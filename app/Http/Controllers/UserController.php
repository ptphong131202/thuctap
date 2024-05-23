<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CanBo;
use App\Enums\UserType;
use App\Enums\Permission;
use App\Http\Requests\CanBoEditRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\SinhVien;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $message = 'Hello';
        return view('user.user_list');
    }

    public function create()
    {
        return view('user.user_update');
    }

    /**
     * Change password view
     * @return void
     * @author ttdat
     * @version 1.0
     */
    public function changePassword()
    {
        return view('user.user_password', ['userid' => auth()->user()->user_id]);
    }


    public function updateInfoSv(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email',
            'sv_ngaysinh' => 'required|date_format:d/m/Y',
        ], [
            'email.email' => 'Email không đúng định dạng',
            'sv_ngaysinh.required' => 'Ngày sinh không được bỏ trống',
            'sv_ngaysinh.date_format' => 'Ngày sinh không đúng định dạng (dd/mm/yyyy)',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        $userPassedData = $request->only(['email']);
        $sinhVienPassedData = $request->only(['sv_ma', 'sv_ngaysinh']);

        $sv_ma =  $sinhVienPassedData["sv_ma"];
        $sv_id = SinhVien::where('sv_ma', $sv_ma)->first()->sv_id;
        $sinhVienModel = SinhVien::find($sv_id);

        $userModel = $sinhVienModel->user;
        $userModel->fill($userPassedData);
        $userModel->type = UserType::SINHVIEN;
        $userModel->save();

        $sinhVienModel->fill($sinhVienPassedData);
        $sinhVienModel->user_id = $userModel->user_id;
        $sinhVienModel->save();


        session()->flash('success_message', 'Cập nhật thông tin thành công!');
        return back();
    }

    /**
     * Change password proccess
     * @param \App\Http\Requests\ChangePasswordRequest $request
     * @return void
     * @author ttdat
     * @version 1.0
     */
    public function updatePassword(ChangePasswordRequest $request)
    {
        $user = auth()->user();
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json([
            'error' => 0,
            'message' => 'Success'
        ]);
    }

    public function permissions()
    {
        $permissions = auth()->user()->permissions()->get()->map(function ($item) {
            return $item->permission_id;
        });
        return response()->json($permissions);
    }

    /**
     * @return void
     * @author ttdat
     * @version 1.0
     */
    public function paginate(Request $request)
    {
        $search = $request->search;
        $danhSachUser = User::join('qlsv_canbo as cb', 'cb.user_id', '=', 'users.user_id')
            ->where(function ($builder) use ($search) {
                $builder->whereRaw('lower(users.name) like lower(?)', "%$search%")
                    ->orWhereRaw('lower(users.email) like lower(?)', "%$search%")
                    ->orWhereRaw('lower(users.username) like lower(?)', "%$search%");
            })
            ->paginate(20)
            ->setPath(route('user.index'))
            ->appends(['search' => $search])
            ->onEachSide(2);
        foreach ($danhSachUser as $user) {
            $user->edit_url = route('user.edit', $user);
        }
        return response()
            ->json($danhSachUser);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CanBoEditRequest $request)
    {
        // Input
        $userPassedData = $request->only(['username', 'password', 'email', 'status']);
        $canBoPassedData = $request->only(['cb_ho', 'cb_ten', 'cb_gioitinh', 'cb_chucvu']);
        $permissionsPassedData = $request->only(['permissions']);

        $userModel = new User;
        DB::transaction(function () use ($userModel, $userPassedData, $canBoPassedData, $permissionsPassedData) {
            $userModel->fill($userPassedData);
            $userModel->password = bcrypt($userModel->password);
            $userModel->type = UserType::CANBO;
            if (isset($canBoPassedData['cb_ho'])) {
                $userModel->name = $canBoPassedData['cb_ho'] . ' ' . $canBoPassedData['cb_ten'];
            } else {
                $userModel->name = $canBoPassedData['cb_ten'];
            }
            $userModel->save();

            $canBoModel = new CanBo;
            $canBoModel->fill($canBoPassedData);
            $canBoModel->user_id = $userModel->user_id;
            $canBoModel->cb_ma = $userModel->username;
            $canBoModel->save();

            $userModel->permissions()->sync($permissionsPassedData['permissions']);
        });


        return response()->json([
            'error' => 0,
            'message' => 'Success',
            'payload' => [
                'url' => route('user.edit', $userModel->user_id),
            ]
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getUser($id)
    {
        $user = User::find($id);

        $permissions = $user->load('canBo')->permissions->map(function ($value) {
            return $value->permission_id;
        });


        $result = [
            'user' => $user,
            'permissions' => $permissions,
            'root' => $user->user_id === __c('super_admin.user_id'),
        ];
        return response()->json($result);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if ($user->user_id != auth()->user()->user_id && !is_super_admin()) {
            abort(403);
        }
        return view('user.user_update', ['userid' => $user->user_id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CanBoEditRequest $request, $id)
    {
        // Input
        $userPassedData = $request->only(['username', 'password', 'email', 'status']);
        $canBoPassedData = $request->only(['cb_ho', 'cb_ten', 'cb_gioitinh', 'cb_chucvu']);
        $permissionsPassedData = $request->only(['permissions']);

        DB::transaction(function () use ($id, $userPassedData, $canBoPassedData, $permissionsPassedData) {
            $userModel = User::find($id);
            $userModel->fill($userPassedData);
            if (isset($userPassedData['password'])) {
                $userModel->password = bcrypt($userModel->password);
            }
            $userModel->type = UserType::CANBO;
            if (isset($canBoPassedData['cb_ho'])) {
                $userModel->name = $canBoPassedData['cb_ho'] . ' ' . $canBoPassedData['cb_ten'];
            } else {
                $userModel->name = $canBoPassedData['cb_ten'];
            }
            $userModel->save();

            $canBoModel = $userModel->canBo;
            $canBoModel->fill($canBoPassedData);
            $canBoModel->user_id = $userModel->user_id;
            $canBoModel->cb_ma = $userModel->username;
            $canBoModel->save();

            if (!$userModel->isSuperAdmin()) {
                $userModel->permissions()->sync($permissionsPassedData['permissions']);
            }

            $user = $userModel;
        });

        return response()->json([
            'error' => 0,
            'message' => 'Success',
            'payload' => [
                'url' => route('user.edit', $id),
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if ($user->user_id === __c('super_admin.user_id')) {
            return response()->json($user, 403);
        } else if ($user->user_id === auth()->user()->user_id) {
            return response()->json($user, 403);
        }

        $user->delete();
        $user->canBo->delete();
        return response()->json($user);
    }
}
