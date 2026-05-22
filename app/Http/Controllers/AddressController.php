<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Services\RajaOngkirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function __construct(protected RajaOngkirService $rajaOngkir) {}

    /**
     * Return provinces list as JSON (for address form dropdown).
     */
    public function provinces(): JsonResponse
    {
        return response()->json([
            'data' => $this->rajaOngkir->getProvinces(),
        ]);
    }

    /**
     * Return cities list for a province as JSON.
     */
    public function cities(string $provinceId): JsonResponse
    {
        return response()->json([
            'data' => $this->rajaOngkir->getCities($provinceId),
        ]);
    }
    /**
     * Store a new address.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'label'          => ['required', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:100'],
            'phone'          => ['required', 'string', 'max:20'],
            'address'        => ['required', 'string', 'max:500'],
            'province'       => ['required', 'string'],
            'city'           => ['required', 'string'],
            'postal_code'    => ['required', 'string', 'max:10'],
            'is_default'     => ['boolean'],
        ]);

        // Province/city values come as "ID|Name" from the dropdown
        [$provinceId, $provinceName] = $this->splitIdName($validated['province']);
        [$cityId,     $cityName]     = $this->splitIdName($validated['city']);

        $user = Auth::user();

        if ($request->boolean('is_default') || $user->addresses()->count() === 0) {
            $user->addresses()->update(['is_default' => false]);
            $isDefault = true;
        } else {
            $isDefault = false;
        }

        $user->addresses()->create([
            'label'          => $validated['label'],
            'recipient_name' => $validated['recipient_name'],
            'phone'          => $validated['phone'],
            'address'        => $validated['address'],
            'province'       => $provinceName,
            'province_id'    => $provinceId,
            'city'           => $cityName,
            'city_id'        => $cityId,
            'postal_code'    => $validated['postal_code'],
            'is_default'     => $isDefault,
        ]);

        return back()->with('success', 'Alamat berhasil ditambahkan.');
    }

    /**
     * Update an existing address.
     */
    public function update(Request $request, Address $address): RedirectResponse
    {
        $this->authorizeAddress($address);

        $validated = $request->validate([
            'label'          => ['required', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:100'],
            'phone'          => ['required', 'string', 'max:20'],
            'address'        => ['required', 'string', 'max:500'],
            'province'       => ['required', 'string'],
            'city'           => ['required', 'string'],
            'postal_code'    => ['required', 'string', 'max:10'],
            'is_default'     => ['boolean'],
        ]);

        [$provinceId, $provinceName] = $this->splitIdName($validated['province']);
        [$cityId,     $cityName]     = $this->splitIdName($validated['city']);

        if ($request->boolean('is_default')) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }

        $address->update([
            'label'          => $validated['label'],
            'recipient_name' => $validated['recipient_name'],
            'phone'          => $validated['phone'],
            'address'        => $validated['address'],
            'province'       => $provinceName,
            'province_id'    => $provinceId,
            'city'           => $cityName,
            'city_id'        => $cityId,
            'postal_code'    => $validated['postal_code'],
            'is_default'     => $request->boolean('is_default'),
        ]);

        return back()->with('success', 'Alamat berhasil diperbarui.');
    }

    /**
     * Delete an address.
     */
    public function destroy(Address $address): RedirectResponse
    {
        $this->authorizeAddress($address);

        $wasDefault = $address->is_default;
        $address->delete();

        // If deleted address was default, promote oldest remaining address
        if ($wasDefault) {
            $next = Auth::user()->addresses()->oldest()->first();
            $next?->update(['is_default' => true]);
        }

        return back()->with('success', 'Alamat berhasil dihapus.');
    }

    /**
     * Set address as default.
     */
    public function setDefault(Address $address): RedirectResponse
    {
        $this->authorizeAddress($address);

        Auth::user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Alamat utama berhasil diperbarui.');
    }

    /**
     * Ensure the authenticated user owns this address.
     */
    private function authorizeAddress(Address $address): void
    {
        abort_if($address->user_id !== Auth::id(), 403);
    }

    /**
     * Split "ID|Name" string into [id, name].
     * Falls back gracefully if no pipe separator present.
     */
    private function splitIdName(string $value): array
    {
        if (str_contains($value, '|')) {
            [$id, $name] = explode('|', $value, 2);
            return [trim($id), trim($name)];
        }
        return [null, trim($value)];
    }
}
