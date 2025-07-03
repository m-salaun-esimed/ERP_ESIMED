<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class FirstLogin extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $routePath = 'first-login';
    protected static ?string $title = 'Complétez votre profil';
    protected static string $view = 'filament.pages.first-login';
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = null;

    public $phone_number;
    public $street;
    public $postal_code;
    public $city;
    public $region;
    public $country;
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
            'street' => $user->street,
            'postal_code' => $user->postal_code,
            'city' => $user->city,
            'region' => $user->region,
            'country' => $user->country,
            'max_annual_revenue' => $user->max_annual_revenue,
            'charge_rate' => $user->charge_rate,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('phone_number')
                ->label('Numéro de téléphone')
                ->tel()
                ->required()
                ->maxLength(20),

            Grid::make(2)->schema([
                Textarea::make('street')
                    ->label('Rue, numéro')
                    ->required()
                    ->rows(3),

                TextInput::make('postal_code')
                    ->label('Code postal')
                    ->required(),

                TextInput::make('city')
                    ->label('Ville')
                    ->required(),

                TextInput::make('region')
                    ->label('Région')
                    ->required(),

                TextInput::make('country')
                    ->label('Pays')
                    ->required(),
            ]),

            TextInput::make('max_annual_revenue')
                ->label('Revenu annuel maximal')
                ->numeric()
                ->required(),

            TextInput::make('charge_rate')
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
        $user->street = $data['street'];
        $user->postal_code = $data['postal_code'];
        $user->city = $data['city'];
        $user->region = $data['region'];
        $user->country = $data['country'];
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
