<x-guest-layout>
    <!-- Session Status -->
    @if(session('status'))
        <div style="background:#E8F5E9;color:#2e7d32;padding:.65rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:.85rem;font-weight:500;border:1px solid #C8E6C9;">
            {{ session('status') }}
        </div>
    @endif

    <div class="login-card">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@email.com">
                @error('email') <div class="login-error">{{ $message }}</div> @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                @error('password') <div class="login-error">{{ $message }}</div> @enderror
            </div>

            <!-- Remember Me -->
            <div class="remember-row">
                <input id="remember_me" type="checkbox" name="remember">
                <label for="remember_me">Ingat saya</label>
            </div>

            <div class="login-actions">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">Lupa password?</a>
                @endif

                <button type="submit" class="btn-login">Masuk</button>
            </div>
        </form>
    </div>
</x-guest-layout>
