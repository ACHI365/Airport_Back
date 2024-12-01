<?php


namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;


class UserController extends Controller
{

    public function listPurchases(Request $request): Collection|\Illuminate\Support\Collection
    {
        $user = $request->user();

        $purchases = Purchase::with(['tickets', 'schedule.route.startStop', 'schedule.route.endStop'])
            ->where('user_id', $user->id)
            ->get();

        return UserService::purchaseDTO($purchases);
    }


    public function listAllPurchases(): Collection|\Illuminate\Http\JsonResponse|\Illuminate\Support\Collection
    {
        $purchases = Purchase::with(['user', 'schedule.route.startStop', 'schedule.route.endStop'])->get();

        return UserService::purchaseDTO($purchases);
    }


    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json($user, 201);
    }

    public function getById($id)
    {
        $user = User::findOrFail($id);

        return response()->json($user);
    }

    public function getAll()
    {
        $users = User::all();

        return response()->json($users);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
        ]);

        return response()->json($user);
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }

}
