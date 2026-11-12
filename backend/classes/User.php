<?php
class User
{
    private PDO $db;
    private ?string $error = null;
    private ?array $authenticatedUser = null;

    public function __construct(PDO $dbconn)
    {
        $this->db = $dbconn;
    }

    public function register(string $username, string $password, string $nama): bool
    {
        $this->error = null;
        try {
            if ($this->usernameExists($username)) {
                $this->error = 'Username sudah digunakan.';
                return false;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            if ($hashedPassword === false) {
                $this->error = 'Password tidak dapat diproses.';
                return false;
            }

            $statement = $this->db->prepare(
                'INSERT INTO users (nama, username, password, role)
                 VALUES (:nama, :username, :password, :role)'
            );
            $statement->execute([
                ':nama' => $nama,
                ':username' => $username,
                ':password' => $hashedPassword,
                ':role' => 'user',
            ]);

            return true;
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            $this->error = $exception->getCode() === '23000'
                ? 'Username sudah digunakan.'
                : 'Pendaftaran gagal. Silakan coba lagi.';
            return false;
        }
    }

    public function login(string $username, string $password): bool
    {
        $this->error = null;
        try {
            $statement = $this->db->prepare(
                'SELECT id, nama, username, password, role
                 FROM users
                 WHERE username = :username
                 LIMIT 1'
            );
            $statement->execute([':username' => $username]);
            $user = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password'])) {
                $this->error = 'Username atau password salah.';
                return false;
            }

            $this->authenticatedUser = $user;
            return true;
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            $this->error = 'Login belum dapat diproses. Silakan coba lagi.';
            return false;
        }
    }

    public function usernameExists(string $username): bool
    {
        $statement = $this->db->prepare(
            'SELECT 1 FROM users WHERE username = :username LIMIT 1'
        );
        $statement->execute([':username' => $username]);
        return $statement->fetchColumn() !== false;
    }

    public function getUserId(): ?int
    {
        return $this->authenticatedUser === null
            ? null
            : (int) $this->authenticatedUser['id'];
    }

    public function getRole(): ?string
    {
        return $this->authenticatedUser['role'] ?? null;
    }

    public function getName(): ?string
    {
        return $this->authenticatedUser['nama'] ?? null;
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}
