<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Request a password reset email</title>
  <style>
    /* Add your CSS styles here */
  </style>
</head>
<body>

  <h1 class="title">Request a password reset email</h1>

  <!-- Session messages cannot be handled by plain HTML; backend needed -->

  <div class="mx-auto max-w-screen-sm card">
    <form action="{{ route('password.email') }}" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

      <!-- CSRF token is backend-generated, so you have to add it manually -->
      <!-- Email -->
      <div class="mb-4">
        <label for="email">Email</label>
        <input type="text" name="email" value="" class="input" />
        <!-- Validation errors require backend to display -->
      </div>

      <!-- Submit Button -->
      <button class="btn" type="submit">Submit</button>
    </form>
  </div>

</body>
</html>
