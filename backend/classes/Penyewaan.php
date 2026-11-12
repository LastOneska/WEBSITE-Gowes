<?php
class Penyewaan
{
    private PDO $db;
    private ?string $error = null;
    private ?int $lastRentalId = null;

    public function __construct(PDO $dbconn)
    {
        $this->db = $dbconn;
    }

    public function sewa(int $userId, int $kendaraanId, int $durasiJam, string $metode): bool
    {
        $this->error = null;
        $this->lastRentalId = null;

        if ($userId < 1 || $kendaraanId < 1 || $durasiJam < 1) {
            $this->error = 'Data penyewaan tidak valid. Durasi harus lebih dari 0 jam.';
            return false;
        }
        if (!in_array($metode, ['tunai', 'transfer', 'e-wallet'], true)) {
            $this->error = 'Metode pembayaran tidak valid.';
            return false;
        }

        try {
            $this->db->beginTransaction();

            $vehicleStatement = $this->db->prepare(
                'SELECT harga_per_jam, stok
                 FROM kendaraan
                 WHERE id = :id
                 FOR UPDATE'
            );
            $vehicleStatement->execute([':id' => $kendaraanId]);
            $vehicle = $vehicleStatement->fetch(PDO::FETCH_ASSOC);

            if ($vehicle === false) {
                $this->db->rollBack();
                $this->error = 'Kendaraan tidak ditemukan.';
                return false;
            }
            if ((int) $vehicle['stok'] < 1) {
                $this->db->rollBack();
                $this->error = 'Stok kendaraan sedang habis.';
                return false;
            }

            $hargaPerJam = (int) $vehicle['harga_per_jam'];
            if ($hargaPerJam > intdiv(2147483647, $durasiJam)) {
                $this->db->rollBack();
                $this->error = 'Total biaya melebihi batas nilai yang dapat disimpan.';
                return false;
            }
            $totalHarga = $hargaPerJam * $durasiJam;
            $rentalStatement = $this->db->prepare(
                "INSERT INTO penyewaan
                    (user_id, kendaraan_id, jam_mulai, durasi_jam, total_harga, status)
                 VALUES
                    (:user_id, :kendaraan_id, CURRENT_TIMESTAMP, :durasi_jam, :total_harga, 'berjalan')"
            );
            $rentalStatement->execute([
                ':user_id' => $userId,
                ':kendaraan_id' => $kendaraanId,
                ':durasi_jam' => $durasiJam,
                ':total_harga' => $totalHarga,
            ]);
            $rentalId = (int) $this->db->lastInsertId();

            $stockStatement = $this->db->prepare(
                'UPDATE kendaraan SET stok = stok - 1 WHERE id = :id AND stok > 0'
            );
            $stockStatement->execute([':id' => $kendaraanId]);
            if ($stockStatement->rowCount() !== 1) {
                throw new RuntimeException('Stok kendaraan berubah saat penyewaan diproses.');
            }

            $paymentStatement = $this->db->prepare(
                'INSERT INTO pembayaran (penyewaan_id, metode, jumlah_bayar)
                 VALUES (:penyewaan_id, :metode, :jumlah_bayar)'
            );
            $paymentStatement->execute([
                ':penyewaan_id' => $rentalId,
                ':metode' => $metode,
                ':jumlah_bayar' => $totalHarga,
            ]);

            $this->db->commit();
            $this->lastRentalId = $rentalId;
            return true;
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            if (!$exception instanceof PDOException && !$exception instanceof RuntimeException) {
                throw $exception;
            }
            error_log($exception->getMessage());
            $this->error = 'Penyewaan gagal diproses. Tidak ada perubahan yang disimpan.';
            return false;
        }
    }

    public function kembalikan(int $penyewaanId, int $userId): bool
    {
        $this->error = null;
        if ($penyewaanId < 1 || $userId < 1) {
            $this->error = 'Data pengembalian tidak valid.';
            return false;
        }

        try {
            $this->db->beginTransaction();

            $rentalStatement = $this->db->prepare(
                'SELECT kendaraan_id, status
                 FROM penyewaan
                 WHERE id = :id AND user_id = :user_id
                 FOR UPDATE'
            );
            $rentalStatement->execute([
                ':id' => $penyewaanId,
                ':user_id' => $userId,
            ]);
            $rental = $rentalStatement->fetch(PDO::FETCH_ASSOC);

            if ($rental === false) {
                $this->db->rollBack();
                $this->error = 'Penyewaan tidak ditemukan atau bukan milik akun ini.';
                return false;
            }
            if ($rental['status'] !== 'berjalan') {
                $this->db->rollBack();
                $this->error = 'Penyewaan ini sudah dikembalikan.';
                return false;
            }

            $updateStatement = $this->db->prepare(
                "UPDATE penyewaan SET status = 'selesai'
                 WHERE id = :id AND user_id = :user_id AND status = 'berjalan'"
            );
            $updateStatement->execute([
                ':id' => $penyewaanId,
                ':user_id' => $userId,
            ]);
            if ($updateStatement->rowCount() !== 1) {
                throw new RuntimeException('Status penyewaan berubah saat pengembalian diproses.');
            }

            $stockStatement = $this->db->prepare(
                'UPDATE kendaraan SET stok = stok + 1 WHERE id = :id'
            );
            $stockStatement->execute([':id' => (int) $rental['kendaraan_id']]);
            if ($stockStatement->rowCount() !== 1) {
                throw new RuntimeException('Kendaraan untuk penyewaan tidak ditemukan.');
            }

            $this->db->commit();
            return true;
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            if (!$exception instanceof PDOException && !$exception instanceof RuntimeException) {
                throw $exception;
            }
            error_log($exception->getMessage());
            $this->error = 'Pengembalian gagal diproses. Tidak ada perubahan yang disimpan.';
            return false;
        }
    }

    public function riwayatUser(int $userId): array
    {
        $statement = $this->db->prepare(
            'SELECT penyewaan.id, penyewaan.user_id, penyewaan.kendaraan_id,
                    penyewaan.jam_mulai, penyewaan.durasi_jam, penyewaan.total_harga,
                    penyewaan.status, kendaraan.nama_kendaraan, kendaraan.harga_per_jam,
                    kategori.nama_kategori, lokasi.nama_lokasi, pembayaran.metode,
                    pembayaran.jumlah_bayar, pembayaran.tgl_bayar
             FROM penyewaan
             JOIN kendaraan ON kendaraan.id = penyewaan.kendaraan_id
             JOIN kategori ON kategori.id = kendaraan.kategori_id
             JOIN lokasi ON lokasi.id = kendaraan.lokasi_id
             LEFT JOIN pembayaran ON pembayaran.penyewaan_id = penyewaan.id
             WHERE penyewaan.user_id = :user_id
             ORDER BY penyewaan.jam_mulai DESC'
        );
        $statement->execute([':user_id' => $userId]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function semuaData(): array
    {
        $statement = $this->db->query(
            'SELECT penyewaan.id, penyewaan.user_id, penyewaan.kendaraan_id,
                    penyewaan.jam_mulai, penyewaan.durasi_jam, penyewaan.total_harga,
                    penyewaan.status, users.nama AS nama_user, users.username,
                    kendaraan.nama_kendaraan, kategori.nama_kategori, lokasi.nama_lokasi,
                    pembayaran.metode, pembayaran.jumlah_bayar, pembayaran.tgl_bayar
             FROM penyewaan
             JOIN users ON users.id = penyewaan.user_id
             JOIN kendaraan ON kendaraan.id = penyewaan.kendaraan_id
             JOIN kategori ON kategori.id = kendaraan.kategori_id
             JOIN lokasi ON lokasi.id = kendaraan.lokasi_id
             LEFT JOIN pembayaran ON pembayaran.penyewaan_id = penyewaan.id
             ORDER BY penyewaan.jam_mulai DESC'
        );

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLastRentalId(): ?int
    {
        return $this->lastRentalId;
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}
