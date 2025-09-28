<h1 class="title">Reset your password</h1>

<div class="mx-auto max-w-screen-sm card">
    <form action="{{ route('password.update')}}" method="post">
        <!-- Replace TOKEN_VALUE with the actual reset token -->
        <input type="hidden" name="token" value="{{$token}}">

        <!-- Email -->
        <div class="mb-4">
            <label for="email">Email</label>
            <input type="text" name="email" value="" class="input">
            <!-- Validation errors can't be shown with plain HTML -->
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password">Password</label>
            <input type="password" name="password" class="input">
        </div>

        <!-- Confirm Password -->
        <div class="mb-8">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" class="input">
        </div>

        <!-- Submit Button -->
        <button class="btn" type="submit">Reset Password</button>
    </form>
</div>
