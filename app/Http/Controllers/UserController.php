<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\IndexUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(private UserService $users) {}

    public function index(IndexUserRequest $request)
    {
        $users = $this->users->paginate(
            $request->search(),
            $request->sortField(),
            $request->sortDirection()
        );

        return Inertia::render('Users/Index', [
            'users' => $users->through(fn ($user) => new UserResource($user)),
            'search' => $request->search(),
            'sort' => $request->sortField(),
            'direction' => $request->sortDirection(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create');
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $this->users->create($request->validated());

            return redirect()->route('users.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            Log::error('User Create Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput()->withErrors([
                'error' => 'Something went wrong: '.$e->getMessage(),
            ]);
        }
    }

    public function edit(string $id)
    {
        return Inertia::render('Users/Edit', [
            'user' => new UserResource($this->users->find($id)),
        ]);
    }

    public function update(UpdateUserRequest $request, string $id)
    {
        $user = $this->users->find($id);

        try {
            $this->users->update($user, $request->validated());

            return redirect()->route('users.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            Log::error('User Update Error', [
                'message' => $e->getMessage(),
                'user_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput()->withErrors([
                'error' => 'Something went wrong: '.$e->getMessage(),
            ]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->users->delete($this->users->find($id));

            return redirect()->route('users.index')->with('success', 'User deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Failed to delete user', [
                'user_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('users.index')->with('error', 'Failed to delete user. Please try again.');
        }
    }

    public function show(string $id)
    {
        return Inertia::render('Users/View', [
            'user' => new UserResource($this->users->find($id)),
        ]);
    }
}
