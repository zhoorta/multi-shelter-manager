<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Concerns\PasswordValidationRules;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * First-run setup wizard: creates the initial admin account while the
 * application has no active users, then becomes unreachable.
 */
class Setup extends Component
{
    use PasswordValidationRules;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Whether the application still needs its first admin account.
     */
    public static function isRequired(): bool
    {
        return User::query()->doesntExist();
    }

    public function mount(): void
    {
        if (! self::isRequired()) {
            $this->redirectRoute('login');
        }
    }

    /**
     * Create the first admin account and log it in.
     */
    public function createAdmin(): void
    {
        abort_unless(self::isRequired(), 403);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            'password' => $this->passwordRules(),
        ]);

        $admin = new User([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_admin' => true,
        ]);
        $admin->email_verified_at = now();
        $admin->save();

        Auth::login($admin);
        session()->regenerate();

        $this->redirectRoute('dashboard', navigate: true);
    }

    #[Layout('layouts::auth')]
    public function render(): View
    {
        return view('livewire.setup')->title(__('Setup'));
    }
}
