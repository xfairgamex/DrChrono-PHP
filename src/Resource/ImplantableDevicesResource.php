<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Implantable Devices - Record implanted medical device information
 *
 * Implantable devices track medical devices implanted in patients,
 * including pacemakers, stents, prosthetics, and other implants,
 * maintaining critical safety and tracking information.
 *
 * API Endpoint: /api/implantable_devices
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#implantable_devices
 */
class ImplantableDevicesResource extends AbstractResource
{
    protected string $resourcePath = '/api/implantable_devices';

    /**
     * List implantable devices
     *
     * @param array $filters Optional filters:
     *   - 'patient' (int): Filter by patient ID
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'since' (string): Filter by update date
     *   - 'status' (string): Filter by status (active, removed, explanted)
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific implantable device
     *
     * @param int|string $deviceId Device ID
     * @return array Device data
     */
    public function get(int|string $deviceId): array
    {
        return parent::get($deviceId);
    }

    /**
     * List devices for a specific patient
     *
     * @param int $patientId Patient ID
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function listByPatient(int $patientId, array $filters = []): PagedCollection
    {
        $filters['patient'] = $patientId;
        return $this->list($filters);
    }

    /**
     * List devices by doctor
     *
     * @param int $doctorId Doctor ID
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function listByDoctor(int $doctorId, array $filters = []): PagedCollection
    {
        $filters['doctor'] = $doctorId;
        return $this->list($filters);
    }

    /**
     * Create a new device record
     *
     * Required fields:
     * - patient (int): Patient ID
     * - device_type (string): Type of device
     * - implant_date (string): Date implanted (YYYY-MM-DD)
     *
     * Optional fields:
     * - doctor (int): Implanting doctor ID
     * - device_identifier (string): Unique device identifier (UDI)
     * - manufacturer (string): Device manufacturer
     * - model_number (string): Model number
     * - serial_number (string): Serial number
     * - lot_number (string): Lot number
     * - expiration_date (string): Expiration date if applicable
     * - anatomic_location (string): Where device is implanted
     * - status (string): Status (active, removed, explanted)
     * - removal_date (string): Date removed if applicable
     * - notes (string): Additional notes
     *
     * @param array $data Device data
     * @return array Created device
     */
    public function createDevice(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update an existing device record
     *
     * @param int $deviceId Device ID
     * @param array $data Updated data
     * @return array Updated device
     */
    public function updateDevice(int $deviceId, array $data): array
    {
        return $this->update($deviceId, $data);
    }

    /**
     * Delete a device record
     *
     * @param int $deviceId Device ID
     * @return void
     */
    public function deleteDevice(int $deviceId): void
    {
        $this->delete($deviceId);
    }

    /**
     * Get active devices for a patient
     *
     * Returns only currently implanted devices
     *
     * @param int $patientId Patient ID
     * @return PagedCollection
     */
    public function getActiveForPatient(int $patientId): PagedCollection
    {
        return $this->listByPatient($patientId, ['status' => 'active']);
    }

    /**
     * Mark device as removed
     *
     * @param int $deviceId Device ID
     * @param string $removalDate Date removed (YYYY-MM-DD)
     * @param string|null $removalReason Reason for removal
     * @return array Updated device
     */
    public function markRemoved(int $deviceId, string $removalDate, ?string $removalReason = null): array
    {
        $data = [
            'status' => 'removed',
            'removal_date' => $removalDate,
        ];

        if ($removalReason !== null) {
            $data['removal_reason'] = $removalReason;
        }

        return $this->updateDevice($deviceId, $data);
    }

    /**
     * Get devices by type
     *
     * @param string $deviceType Type of device
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function getByType(string $deviceType, array $filters = []): PagedCollection
    {
        $filters['device_type'] = $deviceType;
        return $this->list($filters);
    }

    /**
     * Get devices by manufacturer
     *
     * @param string $manufacturer Manufacturer name
     * @param array $filters Additional filters
     * @return array Matching devices
     */
    public function getByManufacturer(string $manufacturer, array $filters = []): array
    {
        $devices = $this->list($filters);
        $matching = [];

        foreach ($devices as $device) {
            if (($device['manufacturer'] ?? '') === $manufacturer) {
                $matching[] = $device;
            }
        }

        return $matching;
    }

    /**
     * Find device by UDI (Unique Device Identifier)
     *
     * @param string $udi Device UDI
     * @return array|null Device or null if not found
     */
    public function findByUdi(string $udi): ?array
    {
        $devices = $this->list();

        foreach ($devices as $device) {
            if (($device['device_identifier'] ?? '') === $udi) {
                return $device;
            }
        }

        return null;
    }
}
