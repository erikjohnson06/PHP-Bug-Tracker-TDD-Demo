<?php

declare(strict_types=1);

namespace App\Entity;

//use DateTimeInterface;

/**
 * BugReport
 *
 * @author erikjohnson06
 */
class BugReport extends Entity {

    private int $id;
    private string $report_type;
    private string $email;
    private ?string $link = null;
    private string $message;
    private $created_at;

    /**
     * @param string $report_type
     * @return BugReport
     */
    public function setReportType(string $report_type): self {
        $this->report_type = $report_type;
        return $this;
    }

    /**
     * @param string $email
     * @return BugReport
     */
    public function setEmail(string $email): self {
        $this->email = $email;
        return $this;
    }

    /**
     * @param string|null $link
     * @return BugReport
     */
    public function setLink(?string $link): self {
        $this->link = $link;
        return $this;
    }

    /**
     * @param string $message
     * @return BugReport
     */
    public function setMessage(string $message): self {
        $this->message = $message;
        return $this;
    }

    /**
     * @param string $created_at
     * @return BugReport
     */
    public function setCreatedAt($created_at): self {
        $this->created_at = $created_at;
        return $this;
    }

    public function getReportType(): string {
        return $this->report_type;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getLink(): ?string {
        return $this->link;
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getId(): int {
        return $this->id;
    }

    public function toArray(): array {

        return [
            "report_type" => $this->getReportType(),
            "email" => $this->getEmail(),
            "link" => $this->getLink(),
            "message" => $this->getMessage(),
            "created_at" => date("Y-m-d H:i:s")
        ];
    }

}
