<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\FirmwareProject;
use App\Models\FirmwareFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FirmwareManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create the superadmin role and user
        $superAdminRole = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        
        // Define other roles so seeder runs don't crash if triggered
        Role::firstOrCreate(['name' => 'author', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin-marketplace', 'guard_name' => 'web']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole($superAdminRole);

        // Fake public storage disk
        Storage::fake('public');
    }

    public function test_admin_can_view_firmware_projects_list()
    {
        $project = FirmwareProject::create([
            'name' => 'Test Project ESP32',
            'device_type' => 'ESP32',
        ]);

        $response = $this->actingAs($this->admin)->get(route('cms.firmware-projects.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Project ESP32');
    }

    public function test_admin_can_create_firmware_project()
    {
        $response = $this->actingAs($this->admin)->post(route('cms.firmware-projects.store'), [
            'name' => 'New ESP32 Project',
            'device_type' => 'ESP32-S3',
            'description' => 'Cool description',
        ]);

        $response->assertRedirect(route('cms.firmware-projects.index'));
        $this->assertDatabaseHas('firmware_projects', [
            'name' => 'New ESP32 Project',
            'device_type' => 'ESP32-S3',
        ]);
    }

    public function test_admin_can_upload_firmware_file()
    {
        $project = FirmwareProject::create([
            'name' => 'Test Project ESP32',
            'device_type' => 'ESP32',
        ]);

        $file = UploadedFile::fake()->create('firmware.bin', 100);

        $response = $this->actingAs($this->admin)->post(route('cms.firmware-files.store'), [
            'firmware_project_id' => $project->id,
            'version' => 'v1.0.0',
            'flash_offset' => '0x1000',
            'file' => $file,
            'changelog' => 'Initial release notes',
        ]);

        $response->assertRedirect(route('cms.firmware-projects.show', $project->id));
        $this->assertDatabaseHas('firmware_files', [
            'firmware_project_id' => $project->id,
            'version' => 'v1.0.0',
            'flash_offset' => '0x1000',
            'changelog' => 'Initial release notes',
        ]);

        $firmwareFile = FirmwareFile::first();
        Storage::disk('public')->assertExists($firmwareFile->file_path);
    }

    public function test_admin_can_edit_and_update_firmware_file_details()
    {
        $project = FirmwareProject::create([
            'name' => 'Test Project ESP32',
            'device_type' => 'ESP32',
        ]);

        $firmwareFile = FirmwareFile::create([
            'firmware_project_id' => $project->id,
            'version' => 'v1.0.0',
            'file_path' => 'firmwares/test/firmware.bin',
            'flash_offset' => '0x1000',
            'changelog' => 'Old release notes',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('cms.firmware-files.update', $firmwareFile->id), [
            'version' => 'v1.0.1-updated',
            'flash_offset' => '0x10000',
            'changelog' => 'New updated release notes',
        ]);

        $response->assertRedirect(route('cms.firmware-projects.show', $project->id));
        $this->assertDatabaseHas('firmware_files', [
            'id' => $firmwareFile->id,
            'version' => 'v1.0.1-updated',
            'flash_offset' => '0x10000',
            'changelog' => 'New updated release notes',
        ]);
    }

    public function test_admin_can_replace_firmware_file_binary()
    {
        $project = FirmwareProject::create([
            'name' => 'Test Project ESP32',
            'device_type' => 'ESP32',
        ]);

        $oldFilePath = 'firmwares/test-project/old.bin';
        Storage::disk('public')->put($oldFilePath, 'old raw data');

        $firmwareFile = FirmwareFile::create([
            'firmware_project_id' => $project->id,
            'version' => 'v1.0.0',
            'file_path' => $oldFilePath,
            'flash_offset' => '0x1000',
            'changelog' => 'Release details',
        ]);

        $newFile = UploadedFile::fake()->create('updated_firmware.bin', 150);

        $response = $this->actingAs($this->admin)->put(route('cms.firmware-files.update', $firmwareFile->id), [
            'version' => 'v1.0.0',
            'flash_offset' => '0x1000',
            'changelog' => 'Updated details',
            'file' => $newFile,
        ]);

        $response->assertRedirect(route('cms.firmware-projects.show', $project->id));

        // Verify the old file was physically deleted
        Storage::disk('public')->assertMissing($oldFilePath);

        // Verify the new file was uploaded
        $firmwareFile->refresh();
        Storage::disk('public')->assertExists($firmwareFile->file_path);
    }
}
