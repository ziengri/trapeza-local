<?php

namespace App\modules\Korzilla\Excel;

class ProcessLog
{
    public const STATUS_NEW = 0;
    public const STATUS_PRI_PARSING = 1;
    public const STATUS_PROCESSING = 2;
    public const STATUS_COMPLITE = 3;
    public const STATUS_ERROR = 4;

    /**
     * @var array
     */
    protected $process = [
        'status' => self::STATUS_NEW,
        'message' => '',
        'error' => '',
        'warnings' => [],
        'procent' => 0
    ];
    /**
     * @var string
     */
    protected $path = '';
    
    /**
     * __construct
     *
     * @param  mixed $path
     * @param  mixed $new
     * @return void
     */
    public function __construct(string $path, bool $new = false)
    {
        $this->path = $path;
        $this->process = $this->setProcessLog($new);
    }
    
    public function save()
    {
        if (!file_put_contents($this->path, json_encode($this->process))) throw new \Exception("Не удалось сохранить процесс");
    }
    /**
     * getProcess
     *
     * @return array
     */
    public function getProcess():array
    {
        return $this->process;
    }
    
    /**
     * setProcent
     *
     * @param  int $procent
     * @return self
     */
    public function setProcent(?float $procent): self
    {
        $this->process['procent'] = $procent;
        return $this;
    }
    
    /**
     * setWarning
     *
     * @param  array $warning
     * @return self
     */
    public function setWarning($warning): self
    {
        if (is_array($warning)) {
            $this->process['warnings'] = array_merge($this->process['warnings'], $warning);
        } else $this->process['warnings'] = $warning;
        
        return $this;
    }
    
    /**
     * setError
     *
     * @param  mixed $error
     * @return self
     */
    public function setError(string $error): self
    {
        $this->process['error'] = $error;
        return $this;
    }
    
    /**
     * setMessage
     *
     * @param  mixed $message
     * @return self
     */
    public function setMessage(string $message): self
    {
        $this->process['message'] = $message;
        return $this;
    }
    
    /**
     * setStatus
     *
     * @param  mixed $status
     * @return self
     */
    public function setStatus($status): self
    {
        $this->process['status'] = $status;
        return $this;
    }
    
    /**
     * setProcessLog
     *
     * @param  bool $new
     * @return array
     */
    public function setProcessLog(bool $new): array
    {
        if ($new || !file_exists($this->path)) return $this->process;

        return json_decode(file_get_contents($this->path), 1);
    }
}
