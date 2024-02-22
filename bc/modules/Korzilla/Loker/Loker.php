<?php
/**
 * @author Олег Хрулёв
 */
namespace App\modules\Korzilla\Loker;

class Loker
{
    private $lockFile;

    public function __construct(string $key)
    {
        $this->setLockFile($key);
    }
    
    public function lock(): bool
    {
        return file_put_contents($this->lockFile, date('U')) !== false;
    }

    /**
     * @param int $timeout в секундах по истечении которых lock не считается
     * 
     * @return bool
     */
    public function isLocked(int $timeout = null): bool
    {
        clearstatcache(true, $this->lockFile);

        $isLocked = file_exists($this->lockFile);

        if ($isLocked && $timeout) {
            $isLocked = filectime($this->lockFile) > date('U') - $timeout;
        }

        return $isLocked;
    }

    public function unlock(): bool
    {
        return unlink($this->lockFile);
    }

    private function setLockFile($key)
    {
        global $DOCUMENT_ROOT, $pathInc;

        $path = $DOCUMENT_ROOT.$pathInc.'/lock';
        if (!file_exists($path)) mkdir($path);

        $this->lockFile = $path."/{$key}.lock";
    }
}