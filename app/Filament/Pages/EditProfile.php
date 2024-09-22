<?php

namespace App\Filament\Pages;

use Exception;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Pages\Concerns;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Auth\Authenticatable;
class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $view = 'filament.pages.edit-profile';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationLabel = 'null';
    public function getTitle(): string
    {
        return 'الملف الشخصي';
    }



    public ?array $profileData = [];
    public ?array $passwordData = [];
    public function mount(): void
    {
        $this->fillForms();
    }
    protected function getForms(): array
    {
        return [
            'editProfileForm',
            'editPasswordForm',
        ];
    }
    public function editProfileForm(Form $form): Form
    {
        return  $form
->schema([

    Forms\Components\Section::make('صورة الملف الشخصي')
        ->description('تحديث صورة ملفك الشخصي ')
        ->schema([
            SpatieMediaLibraryFileUpload::make('avatar_url')->label('الصورة')->hiddenLabel()
                ->disk('public')
                ->maxSize(2048)
                ->conversion('thumb')
                ->image()
                ->openable()
                ->loadingIndicatorPosition('left')
                ->removeUploadedFileButtonPosition('right')
                ->uploadButtonPosition('left')
                ->uploadProgressIndicatorPosition('left')


            ,

        ]),



        Forms\Components\Section::make('بيانات الملف الشخصي')
            ->description('تحديث ملفك الشخصي وعنوان البريد الالكتروني')
            ->schema([
                Forms\Components\TextInput::make('name')->label('اسم المستخدم')
                    ->required(),
                Forms\Components\TextInput::make('email')->label('البريد الالكتروني')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
            ]),
    ])
        ->model($this->getUser())
        ->statePath('profileData');
 }
    public function editPasswordForm(Form $form): Form
    {
        return  $form
->schema([
        Forms\Components\Section::make('تغيير كلمة السر ')
            ->schema([
                Forms\Components\TextInput::make('Current password')
                    ->label('كلمة السر السابقة')
                    ->password()
                    ->required()
                    ->currentPassword(),
                Forms\Components\TextInput::make('password')
                    ->label('كلمة السر الجديدة')

                    ->password()
                    ->required()
                    ->rule(Password::default())
                    ->autocomplete('new-password')
                    ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
                    ->live(debounce: 500)
                    ->same('passwordConfirmation'),
                Forms\Components\TextInput::make('passwordConfirmation')
                    ->label('تأكيد كلمة السر الجديدة')

                    ->password()
                    ->required()
                    ->dehydrated(false),
            ]),
    ])
        ->model($this->getUser())
        ->statePath('passwordData');
 }
    protected function getUser(): Authenticatable & Model
    {
        $user = Filament::auth()->user();
        if (! $user instanceof Model) {
            throw new Exception('The authenticated user object must be an Eloquent model to allow the profile page to update it.');
        }
        return $user;
    }
    protected function fillForms(): void
    {
        $data = $this->getUser()->attributesToArray();
        $this->editProfileForm->fill($data);
        $this->editPasswordForm->fill();
    }
    protected function getUpdateProfileFormActions(): array
    {
        return [
            Action::make('updateProfileAction')
                ->label(__('filament-panels::pages/auth/edit-profile.form.actions.save.label'))
                ->submit('editProfileForm'),
        ];
    }
    protected function getUpdatePasswordFormActions(): array
    {
        return [
            Action::make('updatePasswordAction')
                ->label(__('filament-panels::pages/auth/edit-profile.form.actions.save.label'))
                ->submit('editPasswordForm'),

        ];
    }
    public function updateProfile(): void
    {
        $data = $this->editProfileForm->getState();
        $this->handleRecordUpdate($this->getUser(), $data);
        $this->sendSuccessNotification();
    }
    public function updatePassword(): void
    {
        $data = $this->editPasswordForm->getState();
        $this->handleRecordUpdate($this->getUser(), $data);
        if (request()->hasSession() && array_key_exists('password', $data)) {
            request()->session()->put(['password_hash_' . Filament::getAuthGuard() => $data['password']]);
        }
        $this->editPasswordForm->fill();
        $this->sendSuccessNotification();
    }
    private function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        return $record;
    }
    private function sendSuccessNotification(): void
    {
        Notification::make()
            ->success()
            ->body('تم تحديث الملف الشخصي بنجاح')
            ->title(__('filament-panels::pages/auth/edit-profile.notifications.saved.title'))
            ->send();

    }
}
