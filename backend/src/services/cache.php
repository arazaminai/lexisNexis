<?php
class Cache {
    protected string $cacheDir;

    public function __construct(string $cacheDir = __DIR__ . '/../static/cache') {
        $this->cacheDir = rtrim($cacheDir, '/') . '/';
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    /**
     * Save data to cache
     *
     * @param string $key
     * @param mixed $data
     * @param int $ttl Time to live in seconds (default 600 = 10 min)
     */
    public function set(string $key, $data, int $ttl = 600): void {
        $cacheFile = $this->getCacheFile($key);
        $payload = [
            'expires_at' => time() + $ttl,
            'data' => $data
        ];
        file_put_contents($cacheFile, json_encode($payload));
    }

    /**
     * Get data from cache
     *
     * @param string $key
     * @return mixed|false Returns cached data or false if expired/not found
     */
    public function get(string $key) {
        $cacheFile = $this->getCacheFile($key);
        if (!file_exists($cacheFile)) return false;

        $payload = json_decode(file_get_contents($cacheFile), true);
        if (!$payload || !isset($payload['expires_at'], $payload['data'])) return false;

        if (time() > $payload['expires_at']) {
            unlink($cacheFile); // expired
            return false;
        }

        return $payload['data'];
    }

    /**
     * Delete cache key
     */
    public function delete(string $key): void {
        $cacheFile = $this->getCacheFile($key);
        if (file_exists($cacheFile)) unlink($cacheFile);
    }

    /**
     * Clear entire cache directory
     */
    public function clear(): void {
        $files = glob($this->cacheDir . '*.json');
        foreach ($files as $file) unlink($file);
    }

    /**
     * Generate safe cache file path
     */
    protected function getCacheFile(string $key): string {
        return $this->cacheDir . md5($key) . '.json';
    }
}
