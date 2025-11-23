<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Communication Log Model
 *
 * Represents a communication audit log entry.
 */
class CommLog extends AbstractModel
{
    /**
     * Get log ID
     */
    public function getId(): ?int
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Get patient ID
     */
    public function getPatientId(): ?int
    {
        return $this->data['patient'] ?? null;
    }

    /**
     * Set patient ID
     */
    public function setPatient(int $patientId): self
    {
        $this->data['patient'] = $patientId;
        return $this;
    }

    /**
     * Get doctor ID
     */
    public function getDoctorId(): ?int
    {
        return $this->data['doctor'] ?? null;
    }

    /**
     * Set doctor ID
     */
    public function setDoctor(int $doctorId): self
    {
        $this->data['doctor'] = $doctorId;
        return $this;
    }

    /**
     * Get user ID
     */
    public function getUserId(): ?int
    {
        return $this->data['user'] ?? null;
    }

    /**
     * Set user ID
     */
    public function setUser(int $userId): self
    {
        $this->data['user'] = $userId;
        return $this;
    }

    /**
     * Get office ID
     */
    public function getOfficeId(): ?int
    {
        return $this->data['office'] ?? null;
    }

    /**
     * Set office ID
     */
    public function setOffice(int $officeId): self
    {
        $this->data['office'] = $officeId;
        return $this;
    }

    /**
     * Get communication type
     */
    public function getCommunicationType(): ?string
    {
        return $this->data['communication_type'] ?? null;
    }

    /**
     * Set communication type
     */
    public function setCommunicationType(string $communicationType): self
    {
        $this->data['communication_type'] = $communicationType;
        return $this;
    }

    /**
     * Get direction (inbound/outbound)
     */
    public function getDirection(): ?string
    {
        return $this->data['direction'] ?? null;
    }

    /**
     * Set direction
     */
    public function setDirection(string $direction): self
    {
        $this->data['direction'] = $direction;
        return $this;
    }

    /**
     * Get subject
     */
    public function getSubject(): ?string
    {
        return $this->data['subject'] ?? null;
    }

    /**
     * Set subject
     */
    public function setSubject(string $subject): self
    {
        $this->data['subject'] = $subject;
        return $this;
    }

    /**
     * Get notes
     */
    public function getNotes(): ?string
    {
        return $this->data['notes'] ?? null;
    }

    /**
     * Set notes
     */
    public function setNotes(string $notes): self
    {
        $this->data['notes'] = $notes;
        return $this;
    }

    /**
     * Get duration in minutes
     */
    public function getDurationMinutes(): ?int
    {
        return $this->data['duration_minutes'] ?? null;
    }

    /**
     * Set duration in minutes
     */
    public function setDurationMinutes(int $durationMinutes): self
    {
        $this->data['duration_minutes'] = $durationMinutes;
        return $this;
    }

    /**
     * Get communication date
     */
    public function getDate(): ?string
    {
        return $this->data['date'] ?? null;
    }

    /**
     * Set communication date
     */
    public function setDate(string $date): self
    {
        $this->data['date'] = $date;
        return $this;
    }

    /**
     * Get created timestamp
     */
    public function getCreatedAt(): ?string
    {
        return $this->data['created_at'] ?? null;
    }

    /**
     * Get updated timestamp
     */
    public function getUpdatedAt(): ?string
    {
        return $this->data['updated_at'] ?? null;
    }

    /**
     * Check if this is a phone call
     */
    public function isPhoneCall(): bool
    {
        return ($this->getCommunicationType() ?? '') === 'phone';
    }

    /**
     * Check if this is an email
     */
    public function isEmail(): bool
    {
        return ($this->getCommunicationType() ?? '') === 'email';
    }

    /**
     * Check if this is a text message
     */
    public function isTextMessage(): bool
    {
        return ($this->getCommunicationType() ?? '') === 'text';
    }

    /**
     * Check if this is an in-person communication
     */
    public function isInPerson(): bool
    {
        return ($this->getCommunicationType() ?? '') === 'in_person';
    }

    /**
     * Check if this is a video call
     */
    public function isVideoCall(): bool
    {
        return ($this->getCommunicationType() ?? '') === 'video';
    }

    /**
     * Check if this is inbound communication
     */
    public function isInbound(): bool
    {
        return ($this->getDirection() ?? '') === 'inbound';
    }

    /**
     * Check if this is outbound communication
     */
    public function isOutbound(): bool
    {
        return ($this->getDirection() ?? '') === 'outbound';
    }

    /**
     * Get formatted duration
     *
     * Returns duration in human-readable format (e.g., "15 minutes", "1 hour 30 minutes")
     */
    public function getFormattedDuration(): ?string
    {
        $minutes = $this->getDurationMinutes();

        if ($minutes === null) {
            return null;
        }

        if ($minutes < 60) {
            return $minutes . ' minute' . ($minutes !== 1 ? 's' : '');
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        $formatted = $hours . ' hour' . ($hours !== 1 ? 's' : '');

        if ($remainingMinutes > 0) {
            $formatted .= ' ' . $remainingMinutes . ' minute' . ($remainingMinutes !== 1 ? 's' : '');
        }

        return $formatted;
    }
}
