<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileAvatarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    private function gifUpload(string $name = 'avatar.gif'): UploadedFile
    {
        $gif = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        $path = tempnam(sys_get_temp_dir(), 'av');
        file_put_contents($path, $gif);

        return new UploadedFile($path, $name, 'image/gif', null, true);
    }

    public function test_user_can_upload_and_remove_avatar(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('profile.avatar.update'), ['avatar' => $this->gifUpload()])
            ->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);

        $this->actingAs($user)
            ->delete(route('profile.avatar.remove'))
            ->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertNull($user->avatar);
    }

    public function test_profile_page_renders_with_avatar_form(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('Pilih Foto');
    }

    public function test_avatar_must_be_valid_image(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('profile.avatar.update'), [
                'avatar' => UploadedFile::fake()->create('avatar.txt', 1),
            ])
            ->assertSessionHasErrors('avatar');
    }

    public function test_every_role_can_change_own_avatar(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        foreach (['super-admin', 'admin', 'manager', 'sales', 'marketing', 'teknisi'] as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);

            $this->actingAs($user)
                ->get(route('profile.edit'))
                ->assertOk();

            $this->actingAs($user)
                ->post(route('profile.avatar.update'), ['avatar' => UploadedFile::fake()->image('avatar.png')])
                ->assertRedirect(route('profile.edit'))
                ->assertSessionHasNoErrors();

            $user->refresh();
            $this->assertNotNull($user->avatar, "Role {$role} gagal mengubah avatar.");
        }
    }

    public function test_avatar_max_size_is_5mb(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('profile.avatar.update'), [
                'avatar' => UploadedFile::fake()->image('avatar.gif')->size(5121),
            ])
            ->assertSessionHasErrors('avatar');

        $this->actingAs($user)
            ->post(route('profile.avatar.update'), [
                'avatar' => $this->gifUpload(),
            ])
            ->assertSessionHasNoErrors();
    }
}
