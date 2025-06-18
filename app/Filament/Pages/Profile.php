<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class Profile extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = 'My Account';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.profile';
    protected static ?string $title = 'My Profile';

    public $name;
    public $first_name;
    public $second_name;
    public $birth_date;
    public $email;
    public $phone_number;
    public $max_annual_revenue;
    public $address;
    public $charge_rate;

    public function mount(): void
    {
        $user = Auth::user();

        $this->form->fill([
            'name' => $user->name,
            'first_name' => $user->first_name,
            'second_name' => $user->second_name,
            'birth_date' => $user->birth_date ? $user->birth_date->format('Y-m-d') : null, // HTML date format
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'max_annual_revenue' => $user->max_annual_revenue,
            'address' => $user->address,
            'charge_rate' => $user->charge_rate,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->label('Last Name')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('first_name')
                ->label('First Name')
                ->maxLength(255),

            Forms\Components\TextInput::make('second_name')
                ->label('Middle Name')
                ->maxLength(255),

            Forms\Components\DatePicker::make('birth_date')
                ->label('Date of Birth'),

            Forms\Components\TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('phone_number')
                ->label('Phone Number')
                ->tel()
                ->maxLength(20),

            Forms\Components\TextInput::make('max_annual_revenue')
                ->label('Max Annual Revenue')
                ->numeric(),

            Forms\Components\Textarea::make('address')
                ->label('Address')
                ->rows(3),

            Forms\Components\TextInput::make('charge_rate')
                ->label('Charge Rate')
                ->numeric(),
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();

        $user = Auth::user();

        $user->name = $data['name'];
        $user->first_name = $data['first_name'];
        $user->second_name = $data['second_name'];
        $user->birth_date = $data['birth_date'];
        $user->email = $data['email'];
        $user->phone_number = $data['phone_number'];
        $user->max_annual_revenue = $data['max_annual_revenue'];
        $user->address = $data['address'];
        $user->charge_rate = $data['charge_rate'];

        $user->save();

        Notification::make()
            ->title('Profile updated successfully.')
            ->success()
            ->send();
    }
}
