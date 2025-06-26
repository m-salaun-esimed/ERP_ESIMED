<?php
namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Actions\Concerns\InteractsWithActions;

class FirstLogin extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $routePath = 'first-login';
    protected static ?string $title = 'Complétez votre profil';
    protected static string $view = 'filament.pages.first-login';
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = null;

    public $phone_number;
    public $address;
    public $max_annual_revenue;
    public $charge_rate;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
    
    public function mount(): void
    {
        $user = Auth::user();

        if ($user->first_login_completed) {
            redirect()->route('filament.admin.pages.dashboard')->send();
        }

        $this->form->fill([
            'phone_number' => $user->phone_number,
            'address' => $user->address,
            'max_annual_revenue' => $user->max_annual_revenue,
            'charge_rate' => $user->charge_rate,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('phone_number')
                ->label('Numéro de téléphone')
                ->tel()
                ->required()
                ->maxLength(20),

            Forms\Components\Textarea::make('address')
                ->label('Adresse')
                ->required()
                ->rows(3),

            Forms\Components\TextInput::make('max_annual_revenue')
                ->label('Revenu annuel maximal')
                ->numeric()
                ->required(),

            Forms\Components\TextInput::make('charge_rate')
                ->label('Taux de charges en %')
                ->numeric()
                ->required(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('save')
                ->label('Enregistrer')
                ->action('submit'),
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();
        $user = Auth::user();

        $user->phone_number = $data['phone_number'];
        $user->address = $data['address'];
        $user->max_annual_revenue = $data['max_annual_revenue'];
        $user->charge_rate = $data['charge_rate'];
        $user->first_login_completed = true;

        $user->save();

        Notification::make()
            ->title('Profil complété avec succès.')
            ->success()
            ->send();

        $this->redirect(route('filament.admin.pages.dashboard'));
    }
}
