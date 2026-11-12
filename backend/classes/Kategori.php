<?php
class Kategori
{
    private PDO $db;
    private ?string $error = null;

    public function __construct(PDO $dbconn)
    {
        $this->db = $dbconn;
    }

    public function getAll(): array
    {
        $statement = $this->db->query(
            'SELECT id, nama_kategori, deskripsi
             FROM kategori
             ORDER BY nama_kategori'
        );

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $statement = $this->db->prepare(
            'SELECT id, nama_kategori, deskripsi
             FROM kategori
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute([':id' => $id]);
        $kategori = $statement->fetch(PDO::FETCH_ASSOC);

        return $kategori === false ? null : $kategori;
    }

    public function tambah(string $namaKategori, ?string $deskripsi = null): bool
    {
        $this->error = null;
        $namaKategori = trim($namaKategori);
        $deskripsi = $this->normalizeDescription($deskripsi);

        if ($namaKategori === '' || strlen($namaKategori) > 50) {
            $this->error = 'Nama kategori wajib diisi dan maksimal 50 karakter.';
            return false;
        }

        try {
            $statement = $this->db->prepare(
                'INSERT INTO kategori (nama_kategori, deskripsi)
                 VALUES (:nama_kategori, :deskripsi)'
            );
            $statement->execute([
                ':nama_kategori' => $namaKategori,
                ':deskripsi' => $deskripsi,
            ]);

            return true;
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            $this->error = 'Kategori gagal ditambahkan. Periksa data lalu coba lagi.';
            return false;
        }
    }

    public function ubah(int $id, string $namaKategori, ?string $deskripsi = null): bool
    {
        $this->error = null;
        $namaKategori = trim($namaKategori);
        $deskripsi = $this->normalizeDescription($deskripsi);

        if ($id < 1 || $namaKategori === '' || strlen($namaKategori) > 50) {
            $this->error = 'ID dan nama kategori tidak valid.';
            return false;
        }

        try {
            $statement = $this->db->prepare(
                'UPDATE kategori
                 SET nama_kategori = :nama_kategori, deskripsi = :deskripsi
                 WHERE id = :id'
            );
            $statement->execute([
                ':nama_kategori' => $namaKategori,
                ':deskripsi' => $deskripsi,
                ':id' => $id,
            ]);

            if ($statement->rowCount() === 0 && $this->getById($id) === null) {
                $this->error = 'Kategori tidak ditemukan.';
                return false;
            }

            return true;
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            $this->error = 'Kategori gagal diperbarui. Periksa data lalu coba lagi.';
            return false;
        }
    }

    public function hapus(int $id): bool
    {
        $this->error = null;
        if ($id < 1) {
            $this->error = 'ID kategori tidak valid.';
            return false;
        }

        try {
            $statement = $this->db->prepare('DELETE FROM kategori WHERE id = :id');
            $statement->execute([':id' => $id]);

            if ($statement->rowCount() === 0) {
                $this->error = 'Kategori tidak ditemukan.';
                return false;
            }

            return true;
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            $this->error = $exception->getCode() === '23000'
                ? 'Kategori tidak dapat dihapus karena masih digunakan oleh kendaraan.'
                : 'Kategori gagal dihapus. Silakan coba lagi.';
            return false;
        }
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    private function normalizeDescription(?string $description): ?string
    {
        if ($description === null) {
            return null;
        }

        $description = trim($description);
        return $description === '' ? null : $description;
    }
}
