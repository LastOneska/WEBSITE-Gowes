<?php
class Kendaraan
{
    private PDO $db;
    private ?string $error = null;

    public function __construct(PDO $dbconn)
    {
        $this->db = $dbconn;
    }

    public function getAll(?int $kategoriId = null, ?int $lokasiId = null): array
    {
        $conditions = [];
        $parameters = [];

        if ($kategoriId !== null && $kategoriId > 0) {
            $conditions[] = 'kendaraan.kategori_id = :kategori_id';
            $parameters[':kategori_id'] = $kategoriId;
        }
        if ($lokasiId !== null && $lokasiId > 0) {
            $conditions[] = 'kendaraan.lokasi_id = :lokasi_id';
            $parameters[':lokasi_id'] = $lokasiId;
        }

        $sql = 'SELECT kendaraan.id, kendaraan.kategori_id, kendaraan.lokasi_id,
                       kendaraan.nama_kendaraan, kendaraan.harga_per_jam, kendaraan.stok,
                       kategori.nama_kategori, lokasi.nama_lokasi
                FROM kendaraan
                JOIN kategori ON kategori.id = kendaraan.kategori_id
                JOIN lokasi ON lokasi.id = kendaraan.lokasi_id';
        if ($conditions !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= ' ORDER BY kendaraan.nama_kendaraan';

        $statement = $this->db->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $statement = $this->db->prepare(
            'SELECT kendaraan.id, kendaraan.kategori_id, kendaraan.lokasi_id,
                    kendaraan.nama_kendaraan, kendaraan.harga_per_jam, kendaraan.stok,
                    kategori.nama_kategori, lokasi.nama_lokasi, lokasi.alamat, lokasi.kota
             FROM kendaraan
             JOIN kategori ON kategori.id = kendaraan.kategori_id
             JOIN lokasi ON lokasi.id = kendaraan.lokasi_id
             WHERE kendaraan.id = :id
             LIMIT 1'
        );
        $statement->execute([':id' => $id]);
        $kendaraan = $statement->fetch(PDO::FETCH_ASSOC);

        return $kendaraan === false ? null : $kendaraan;
    }

    public function tambah(
        string $namaKendaraan,
        int $kategoriId,
        int $lokasiId,
        int $hargaPerJam,
        int $stok
    ): bool {
        $this->error = null;
        $namaKendaraan = trim($namaKendaraan);
        if (!$this->validVehicleData($namaKendaraan, $kategoriId, $lokasiId, $hargaPerJam, $stok)) {
            return false;
        }

        try {
            $statement = $this->db->prepare(
                'INSERT INTO kendaraan
                    (kategori_id, lokasi_id, nama_kendaraan, harga_per_jam, stok)
                 VALUES
                    (:kategori_id, :lokasi_id, :nama_kendaraan, :harga_per_jam, :stok)'
            );
            $statement->execute([
                ':kategori_id' => $kategoriId,
                ':lokasi_id' => $lokasiId,
                ':nama_kendaraan' => $namaKendaraan,
                ':harga_per_jam' => $hargaPerJam,
                ':stok' => $stok,
            ]);

            return true;
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            $this->error = $exception->getCode() === '23000'
                ? 'Kategori atau lokasi yang dipilih tidak ditemukan.'
                : 'Kendaraan gagal ditambahkan. Periksa data lalu coba lagi.';
            return false;
        }
    }

    public function ubah(
        int $id,
        string $namaKendaraan,
        int $kategoriId,
        int $lokasiId,
        int $hargaPerJam,
        int $stok
    ): bool {
        $this->error = null;
        $namaKendaraan = trim($namaKendaraan);
        if ($id < 1 || !$this->validVehicleData($namaKendaraan, $kategoriId, $lokasiId, $hargaPerJam, $stok)) {
            if ($this->error === null) {
                $this->error = 'ID kendaraan tidak valid.';
            }
            return false;
        }

        try {
            $statement = $this->db->prepare(
                'UPDATE kendaraan
                 SET kategori_id = :kategori_id,
                     lokasi_id = :lokasi_id,
                     nama_kendaraan = :nama_kendaraan,
                     harga_per_jam = :harga_per_jam,
                     stok = :stok
                 WHERE id = :id'
            );
            $statement->execute([
                ':kategori_id' => $kategoriId,
                ':lokasi_id' => $lokasiId,
                ':nama_kendaraan' => $namaKendaraan,
                ':harga_per_jam' => $hargaPerJam,
                ':stok' => $stok,
                ':id' => $id,
            ]);

            if ($statement->rowCount() === 0 && $this->exists($id) === false) {
                $this->error = 'Kendaraan tidak ditemukan.';
                return false;
            }

            return true;
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            $this->error = $exception->getCode() === '23000'
                ? 'Kategori atau lokasi yang dipilih tidak ditemukan.'
                : 'Kendaraan gagal diperbarui. Periksa data lalu coba lagi.';
            return false;
        }
    }

    public function hapus(int $id): bool
    {
        $this->error = null;
        if ($id < 1) {
            $this->error = 'ID kendaraan tidak valid.';
            return false;
        }

        try {
            $statement = $this->db->prepare('DELETE FROM kendaraan WHERE id = :id');
            $statement->execute([':id' => $id]);

            if ($statement->rowCount() === 0) {
                $this->error = 'Kendaraan tidak ditemukan.';
                return false;
            }

            return true;
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            $this->error = $exception->getCode() === '23000'
                ? 'Kendaraan tidak dapat dihapus karena memiliki riwayat penyewaan.'
                : 'Kendaraan gagal dihapus. Silakan coba lagi.';
            return false;
        }
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    private function validVehicleData(
        string $namaKendaraan,
        int $kategoriId,
        int $lokasiId,
        int $hargaPerJam,
        int $stok
    ): bool {
        if (
            $namaKendaraan === ''
            || strlen($namaKendaraan) > 100
            || $kategoriId < 1
            || $lokasiId < 1
            || $hargaPerJam < 1
            || $stok < 0
        ) {
            $this->error = 'Nama, kategori, lokasi, tarif, atau stok kendaraan tidak valid.';
            return false;
        }

        return true;
    }

    private function exists(int $id): bool
    {
        $statement = $this->db->prepare('SELECT 1 FROM kendaraan WHERE id = :id LIMIT 1');
        $statement->execute([':id' => $id]);
        return $statement->fetchColumn() !== false;
    }
}
