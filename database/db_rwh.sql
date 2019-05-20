-- phpMyAdmin SQL Dump
-- version 4.8.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 20 Bulan Mei 2019 pada 10.13
-- Versi server: 10.1.31-MariaDB
-- Versi PHP: 7.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_rwh`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bankmember`
--

CREATE TABLE `bankmember` (
  `id` int(11) NOT NULL,
  `ktp` varchar(100) NOT NULL,
  `bank_id` int(11) UNSIGNED NOT NULL,
  `cabbank` varchar(100) NOT NULL,
  `norek` varchar(100) NOT NULL,
  `noatm` varchar(100) NOT NULL,
  `nobuku` varchar(100) NOT NULL,
  `creator` varchar(100) NOT NULL,
  `status` varchar(100) DEFAULT NULL,
  `p_status` varchar(100) DEFAULT NULL,
  `id_jurnal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `demo_device`
--

CREATE TABLE `demo_device` (
  `device_name` varchar(50) NOT NULL,
  `sn` varchar(50) NOT NULL,
  `vc` varchar(50) NOT NULL,
  `ac` varchar(50) NOT NULL,
  `vkey` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `demo_device`
--

INSERT INTO `demo_device` (`device_name`, `sn`, `vc`, `ac`, `vkey`) VALUES
('Device 2', 'C700F001339', '7901D3C13E34109', 'VPFAAB943C33362467D451A0', 'AD090B9CB550CD9164F5844C369C3DB0'),
('Device RWH 2', 'J620E26081', '2D3542EFA9A5680', 'UWR939ACEB14BE7DCFC34RRQ', '19567BA7583858BB9FCBC38C5E3464DE'),
('Device RWH 1', 'J620E26928', '41BCD1E514123F5', 'NQ4E119EE647971FF39ADLM0', 'D9A26C2007522F8BF8C3B83A52B021C3');

-- --------------------------------------------------------

--
-- Struktur dari tabel `demo_finger`
--

CREATE TABLE `demo_finger` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `finger_id` int(11) UNSIGNED NOT NULL,
  `finger_data` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `demo_finger`
--

INSERT INTO `demo_finger` (`user_id`, `finger_id`, `finger_data`, `created_at`, `updated_at`) VALUES
(15, 1, '525133D4D5C9297A871BD3C8C2052D00F82701C82AE3735CC0413709AB7170F6145592FA279BEBBF05D74D8719EA2A80277D750438546B45F9BB22A8198431BE97A52D5500D2F2168770EC62334DD5411649899CF4F37078FFAA414881F694AB63323FA289EC76CF75C44B4993E8675043526CD0CC60192738912FD84B93A8C1272BCDBD8BE868B09C80FBDFEECB4064702EE1959B9ACDEE88374A4D747F99C6EB8F89908A37B92F2907E67A1117CDCEC6505EE0F308CE5213CE04DFDBC497E7A8BFFA1298557C74F9CFA5F69B378DA91C08AE603613F652BB7223C366476F4B22515493B9F2D3CA87A3CA55C22DDD3DD0F5D26F0209C9A202A66751175C7365ADF8C979AA883DCA2BF808F8576FC16CFB2553FE661896F211567A19495895881E5C5E082260EA34BDAECCB422DBCBF294DB09BCFE97A6FA31F6F643634240DFF44D13A66F00F88101C82AE3735CC0413709AB71F09F145592BF7AB1349E5BB84269B9D1B847DE01F7F864DC836EC17986CC4A0AEE6F13A5A6254E0447AD47BE0C3853459BE83ED762BDDAC889849F0132E994590513E434D5A030BE18EA92E1C7E5FC29176DCBFDF3BF7411470B5F4496DBA0EA8465C75220B6247E2A91B639ADDE4F41F4AA58D20849DC2AFFBF240CCB25621A0E71C4CDE46A279768FB4A688134F2CD3455BB268F4ED0857519420CDDAAD52AA4B61C07046DDD4696194AFCC1DEFEA04CE72285D91C19F2F737A14656D06803219C026081B8FF56A813A1DCD1DF00A34444AD15D395D6AD643575B575517751B2C02B02F4C0EC22B13F55A1033C491A8F1DCCAFDAE0B68E09218749E92D0AF13CB2821D8E02AA30C228A6D9DDE5AF670ECAF0871F0FAAEB53EC118CA6B0A122DCB141F5EB4AC3ABF556E6C6E22151B897E8D8F98920A308CACD11492BB6BECDAE6F41DB31547CFAF59B2CB5273A21D662B61181A72302FA4984A7E66910734C5BBD9DCB2B49519C704F6BD2C20F3CD2FFE68880AAA86F00F87F01C82AE3735CC0413709AB7130971455927654C678B36549BB299A9D0421536263709853CE3AA26CC6946C5E5E2ACF819F0320A2E08D97A78FD8710A5BB4B3A42DECCEC0AACAE96E32F02918B1C8FBD501F3DA08D78F25C69897A8826F46690EE8E12E499CB33CDF790F1BC4A52B3367CCD3722C35DD0244EF4E78070DE473A5782AF0A52CE5E1A789DA762DB7C015FDA647E9B5AAB9F373BFF260A1160FEE9FF879023E8BED427C3EB7109AC8BDC15CE39D5600B7A968175306D479F497BBD91F68287086ADBA97861943A181D6AEB982F867B2515E797EC36AEEB9C66F02D841D910E1AFDE4B7016C284FDA09E1A0FF07ED32A808006DEFAC8E55A25A082739750C93546BB79BD96EADC7DF1BEDC5BD0AB3478E1FAC499AD134E420482F226A6B347732D63BA9BB1E0ECFA29D7D8D87F6D0B3103EE4A47E6E633C16671E4A749538DBCB9319E33B96959DCAD2B42CB1A3C1F6E64BE216C4D5BA5A53B9F47A966B7BDC7826F2425DD4775486F908E9A78A57DF3A398CA6F31C6A243DC546E546F00E85C01C82AE3735CC0413709AB71F0F31455923CF311636C6F142CB3C776C3C05DB033C14E2FD08FA48DD3B3553194CB7CECD6A3A946B9E593CCB52C324B4407541BEF031FD7322AABCB022251463731786996A1C00E72E506993E4DFCA25663B9E79DF2EA7BE71D96BE7D4C1577455E3053DA9E021D86E88913834F53675B631E61717636B67616F60252248ECD83DCA3275CDD269831CD8BCD0D476D5C23C761431DB0C0455DF3351B6C03D56E6C5500FA21283C696A8453D7A29B129E83529B85DE91086BCFB8AA272B7C89147A4DE4215FE727838E68143A45698E0DF69F60A9683ACD6E33B8BF04C5C82B685E7949B48A264B24B677EE758CC092C58D4F0218BBE8F56398F438D5FBCB21DE4F8D45B09ADE0A920D61E8C21054D1E855268083F8BFC43F6B507A9EBB5F41793EC35D9DA72D7582CBAC8FFBFE045AA1071FCE3A86ED592772612B05E11DBADAB1911F8CB5B999DA01962FE22024207C596F000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000', '2019-05-14 03:40:05', '2019-05-14 03:40:05'),
(16, 1, '545247A3A0B82A09F069A2C8B7762C00F8F400C82AE3735CC0413709AB7130EA14559260593D679FBF8376EB6895FE480EDCE229121684B3602D1227F6B80A0C0AEEB93B211A235E77E916E877A2888FAE773A84565C9F425DFE8AFB83A6DABA616BCF214EE124C6BAF3CFD2F2CAE2E4819F8839B37D26E0EDBAB37805AC91DDB63E77C83A31621A2CAA7F069D4307DB5C7F16ADE3B61F9DE33490F2430A73B79F0A46D4277EF42FC642297C04BFAA9EF6DE5FAA3581F0C79FEDE93BA4DEEF9638512F58C93D5DF6FDC9FA718F0987B31A887C8C77927450F92A7DC35269AC6A3D4FA08DB05259B2A42617B7F0A5955445F139D66ADBAAE2D049A257BDE320A0F384DDE0E41098E8BEC4C15756D4F755FA6F00F86701C82AE3735CC0412F1A8155A5034D7A393EFF2C17189E21407E04A8BC7FCE8A5AB5C10EDC86B7A1636C08E2832765AEC34D02A8CDBB05CD2EFB7D89A5355DB68CB7A17E3A36EEDC3A6099E2DB5B05C51393FAFAFE5142D7188FC88B01106C398965149CAE8BDC35E7F817FBD838C4262F66FED4D8649DDFD95740932CB3C2E33AA633AA785B859D7604720EE4692CC873AD7CB6CB2ABBE4C3F71DD37860D8D48416B0148DFD703D61ACC3178E36A2EE7BBBDA4630173B38C05185134F250601A784335F301D66A2A4A820B77B8AE278ABB47810D4F5135D471B40F2511577B58637F75BF74982C7F3A0BB7F4553ABAB03959A0C681B394C369073F3CDD27B59FFD03B68358402686F14CDF3F2BD42621E98CA8FA8A9016E62B2CE73082AB998EB64DFACF8D50F2E2BF8F9E7B378C9C52E8562B93E164ED376FBDA80313FA63CE90FEAA6EC921F58BDFEB10E782827F7EA3A756836819038BE5CE1EA0A832C113BBBD55C4C59FE736F00F8DE00C82AE3735CC0413709AB7130E914559202CBD614AAAFC5BC04C4503FCCFF3177D5AFBBE45296774FCDFC3128157074A4FD65FC8162D080B78F088B44FD2928CB2D2C58D2B2E39A8A0D25469F19EE006732C1C0013E24047D89E190F8BCA911D4C0657BB528FC43F8B0DE7FCF933182EE91D41A4B3DEF7651F366A611E8CD95AF4A3F692DAB230883A6FF343C5BC1DFF54FA96DDE2910CA626912828A89D675217B167D10EB4FA6C5FD3EFB0D5014BCB4487FE0D1F7322205A389816624474EAC5CA8AB82686697FFD05877F5AA3706AB144346DFAB296C15EA9633423B7E6F00E8F400C82AE3735CC0413709AB7130EA14559260593D679FBF8376EB6895FE480EDCE229121684B3602D1227F6B80A0C0AEEB93B211A235E77E916E877A2888FAE773A84565C9F425DFE8AFB83A6DABA616BCF214EE124CF8839B37D26E0EDBAB37805AC91DDB63E77C83A31621A2CAA7F069D4307DB5C7F16ADE3B61F9DE33490F2430A73B79F0A46D4277EF42FC642297C04BFAA9EF6DE5FAA3581F0C79FEDE93BA4DEEF9638512F58C93D5DF6FDC9FA718F0987B31A887C8C77927450F92A7DC35269AC6A3D4FA08DB05259B2A42617B7F0A5955445F139D66ADBAAE2D049A257BDE320A0F384DDE0E41098E8BEC4C15756D4F755FA6F000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000', '2019-05-15 22:17:06', '2019-05-15 22:17:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `demo_log`
--

CREATE TABLE `demo_log` (
  `log_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_name` varchar(50) NOT NULL,
  `data` text NOT NULL COMMENT 'sn+pc time',
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `demo_log`
--

INSERT INTO `demo_log` (`log_time`, `user_name`, `data`, `keterangan`, `created_at`, `updated_at`) VALUES
('2019-05-16 02:38:54', 'superadmin', '2019-05-16 09:38:54 (PC Time) | J620E26928 (SN)', NULL, '2019-05-15 19:38:54', '2019-05-15 19:38:54'),
('2019-05-16 02:40:02', 'superadmin', '2019-05-16 09:40:02 (PC Time) | J620E26928 (SN)', 'masuk', '2019-05-15 19:40:02', '2019-05-15 19:40:02'),
('2019-05-16 03:43:27', 'superadmin', '2019-05-16 10:43:27 (PC Time) | J620E26928 (SN)', NULL, '2019-05-15 20:43:27', '2019-05-15 20:43:27'),
('2019-05-16 03:46:28', 'superadmin', '2019-05-16 10:46:28 (PC Time) | J620E26928 (SN)', '\"masuk', '2019-05-15 20:46:28', '2019-05-15 20:46:28'),
('2019-05-16 05:10:46', 'superadmin', '2019-05-16 12:10:46 (PC Time) | J620E26081 (SN)', 'masuk', '2019-05-15 22:10:46', '2019-05-15 22:10:46'),
('2019-05-16 06:15:38', 'superadmin', '2019-05-16 13:15:38 (PC Time) | J620E26081 (SN)', 'masuk', '2019-05-15 23:15:38', '2019-05-15 23:15:38'),
('2019-05-16 06:17:58', 'superadmin', '2019-05-16 13:17:58 (PC Time) | J620E26081 (SN)', 'pulang', '2019-05-15 23:17:58', '2019-05-15 23:17:58'),
('2019-05-16 07:07:05', 'ddd', '2019-05-16 14:07:04 (PC Time) | J620E26081 (SN)', 'masuk', '2019-05-16 00:07:05', '2019-05-16 00:07:05'),
('2019-05-16 07:07:22', 'ddd', '2019-05-16 14:07:22 (PC Time) | J620E26081 (SN)', 'masuk', '2019-05-16 00:07:22', '2019-05-16 00:07:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `demo_user`
--

CREATE TABLE `demo_user` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `demo_user`
--

INSERT INTO `demo_user` (`user_id`, `user_name`, `created_at`, `updated_at`) VALUES
(15, 'superadmin', '2019-05-14 10:10:55', '2019-05-14 10:10:55'),
(16, 'ddd', '2019-05-14 10:17:31', '2019-05-14 10:17:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jenismapping`
--

CREATE TABLE `jenismapping` (
  `id` int(11) UNSIGNED NOT NULL,
  `mapping_id` int(11) UNSIGNED DEFAULT NULL,
  `jenis` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `jenismapping`
--

INSERT INTO `jenismapping` (`id`, `mapping_id`, `jenis`) VALUES
(1, 1, 'create'),
(2, 1, 'update'),
(3, 1, 'delete');

-- --------------------------------------------------------

--
-- Struktur dari tabel `map_purchase`
--

CREATE TABLE `map_purchase` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `sup_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `menumapping`
--

CREATE TABLE `menumapping` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `submodul_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `menumapping`
--

INSERT INTO `menumapping` (`id`, `user_id`, `submodul_id`, `created_at`, `updated_at`) VALUES
(1, 15, 'CRCS', '2019-05-02 05:01:12', '2019-05-02 05:01:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `perusahaanmember`
--

CREATE TABLE `perusahaanmember` (
  `id` int(11) NOT NULL,
  `ktp` varchar(100) NOT NULL,
  `noid` varchar(100) NOT NULL,
  `passid` varchar(100) NOT NULL,
  `perusahaan_id` int(11) UNSIGNED NOT NULL,
  `creator` varchar(100) NOT NULL,
  `posisi` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblavail`
--

CREATE TABLE `tblavail` (
  `id` int(1) NOT NULL,
  `bulan` int(2) DEFAULT NULL,
  `tahun` int(4) DEFAULT NULL,
  `p10` int(3) DEFAULT NULL,
  `n8bv` int(3) DEFAULT NULL,
  `d10` int(3) DEFAULT NULL,
  `n8` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblbank`
--

CREATE TABLE `tblbank` (
  `id` int(11) UNSIGNED NOT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `kode` varchar(10) DEFAULT NULL,
  `icon` varchar(199) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tblbank`
--

INSERT INTO `tblbank` (`id`, `nama`, `kode`, `icon`) VALUES
(1, 'Bank Mandiri', 'mandiri', 'mandiri.png'),
(2, 'Bank BCA', 'bca', 'bca.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblbonus`
--

CREATE TABLE `tblbonus` (
  `id_bonus` int(11) NOT NULL,
  `member_id` varchar(100) NOT NULL,
  `bulan` int(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `bonus` double NOT NULL,
  `creator` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblbonusbayar`
--

CREATE TABLE `tblbonusbayar` (
  `id_bonus` int(11) NOT NULL,
  `no_rek` varchar(100) NOT NULL,
  `tgl` varchar(100) NOT NULL,
  `bulan` int(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `bonus` double NOT NULL,
  `creator` varchar(100) NOT NULL,
  `id_jurnal` int(11) DEFAULT '0',
  `bank` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblbonusgagal`
--

CREATE TABLE `tblbonusgagal` (
  `id` int(11) NOT NULL,
  `ktp` varchar(100) NOT NULL,
  `member_id` varchar(100) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `bulan` int(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `bonus` double NOT NULL,
  `creator` varchar(100) NOT NULL,
  `perusahaan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblcoa`
--

CREATE TABLE `tblcoa` (
  `id` int(11) NOT NULL,
  `grup` int(11) UNSIGNED NOT NULL,
  `AccNo` varchar(100) NOT NULL,
  `AccName` varchar(100) NOT NULL,
  `SaldoNormal` varchar(100) NOT NULL,
  `StatusAccount` varchar(100) NOT NULL,
  `SaldoAwal` double NOT NULL,
  `company_id` int(11) UNSIGNED NOT NULL,
  `StatusAcc` varchar(100) NOT NULL,
  `AccParent` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblcoagrup`
--

CREATE TABLE `tblcoagrup` (
  `id` int(11) UNSIGNED NOT NULL,
  `grup` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblcompany`
--

CREATE TABLE `tblcompany` (
  `company_id` int(11) UNSIGNED NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `company_address` varchar(100) NOT NULL,
  `company_phone` varchar(30) NOT NULL,
  `company_email` varchar(30) NOT NULL,
  `company_est` int(11) NOT NULL,
  `company_ceo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tblcompany`
--

INSERT INTO `tblcompany` (`company_id`, `company_name`, `company_address`, `company_phone`, `company_email`, `company_est`, `company_ceo`) VALUES
(1, 'Royal Warehouse', '', '', '', 0, '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblcustomernew`
--

CREATE TABLE `tblcustomernew` (
  `id` int(11) NOT NULL,
  `cid` varchar(100) NOT NULL,
  `apname` varchar(1000) NOT NULL,
  `apbp` varchar(1000) NOT NULL,
  `apbd` varchar(1000) NOT NULL,
  `apjt` varchar(1000) NOT NULL,
  `apphone` varchar(100) NOT NULL,
  `apfax` varchar(100) NOT NULL,
  `apidc` varchar(100) NOT NULL,
  `apidcn` varchar(100) NOT NULL,
  `apidce` varchar(100) NOT NULL,
  `apemail` varchar(100) NOT NULL,
  `apadd` varchar(100) NOT NULL,
  `cicn` varchar(100) NOT NULL,
  `cicg` varchar(100) NOT NULL,
  `cilob` varchar(100) NOT NULL,
  `ciadd` varchar(1000) NOT NULL,
  `cicty` varchar(100) NOT NULL,
  `cizip` varchar(100) NOT NULL,
  `cipro` varchar(100) NOT NULL,
  `ciweb` varchar(100) NOT NULL,
  `ciemail` varchar(100) NOT NULL,
  `cinpwp` varchar(100) NOT NULL,
  `ciphone` varchar(100) NOT NULL,
  `cifax` varchar(100) NOT NULL,
  `creator` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbldatakota`
--

CREATE TABLE `tbldatakota` (
  `kota_id` int(11) NOT NULL,
  `kode_pusdatin_prov` int(11) NOT NULL,
  `provinsi` varchar(100) NOT NULL,
  `kode_pusdatin_kota` int(11) NOT NULL,
  `kab_kota` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblemployee`
--

CREATE TABLE `tblemployee` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(30) NOT NULL,
  `bck_pass` varchar(199) DEFAULT NULL,
  `password` varchar(199) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `login_status` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `nip` varchar(100) NOT NULL,
  `address` varchar(100) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `ktp` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `creator` varchar(100) DEFAULT NULL,
  `tmpt_lhr` varchar(100) NOT NULL,
  `tgl_lhr` varchar(100) NOT NULL,
  `mulai_kerja` date NOT NULL,
  `start_work2` varchar(100) DEFAULT NULL,
  `bank` varchar(100) DEFAULT NULL,
  `norek` varchar(100) DEFAULT NULL,
  `sima` varchar(100) DEFAULT NULL,
  `simb` varchar(100) DEFAULT NULL,
  `simc` varchar(100) DEFAULT NULL,
  `npwp` varchar(100) DEFAULT NULL,
  `bpjs` varchar(100) DEFAULT NULL,
  `scanktp` varchar(100) DEFAULT NULL,
  `scansima` varchar(100) DEFAULT NULL,
  `scansimb` varchar(100) DEFAULT NULL,
  `scansimc` varchar(100) DEFAULT NULL,
  `scannpwp` varchar(100) DEFAULT NULL,
  `scanbpjs` varchar(100) DEFAULT NULL,
  `scanfoto` varchar(100) DEFAULT NULL,
  `work_start` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tblemployee`
--

INSERT INTO `tblemployee` (`id`, `username`, `bck_pass`, `password`, `last_login`, `login_status`, `name`, `nip`, `address`, `phone`, `ktp`, `email`, `company_id`, `creator`, `tmpt_lhr`, `tgl_lhr`, `mulai_kerja`, `start_work2`, `bank`, `norek`, `sima`, `simb`, `simc`, `npwp`, `bpjs`, `scanktp`, `scansima`, `scansimb`, `scansimc`, `scannpwp`, `scanbpjs`, `scanfoto`, `work_start`, `created_at`, `updated_at`) VALUES
(15, 'superadmin', 'superadmin', '$2y$12$d0J8GKTp5FQOt9E79BBO1uDJgeb9of8QThNdf/RdQ1SY300VcPfVC', '2015-09-07 00:00:00', 1, 'Superadmin', 'SA', 'SA', 'SA', 'SA', 'dodo@pradanatechnology.com', 0, 'SA', '', '', '0000-00-00', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'noimage.jpg', 'noimage.jpg', 'noimage.jpg', 'noimage.jpg', 'noimage.jpg', 'noimage.jpg', 'SA.jpg', NULL, '2019-05-09 07:21:31', '2019-05-09 07:21:31'),
(16, 'ddd', 'ddd', '$2y$10$IDkBDGd6ywFWxppk3sADiOklU2OpyENgDl1bnSg9AUlX0aKJhU2HW', NULL, 1, 'Alvin Khair', 'Alvin Khair', 'fasas', '082216418599', '321312312', 'khairalvin@gmail.com', NULL, NULL, 'Palembang', '2019-04-11', '2019-04-19', NULL, 'mandiri', '3123', NULL, NULL, NULL, NULL, NULL, 'noimage.jpg', 'noimage.jpg', 'noimage.jpg', 'noimage.jpg', 'noimage.jpg', 'noimage.jpg', 'Alvin Khair.PNG', NULL, '2019-04-30 01:23:23', '2019-04-30 01:23:23');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblemployeerole`
--

CREATE TABLE `tblemployeerole` (
  `id` bigint(20) NOT NULL,
  `username` varchar(30) NOT NULL,
  `company_id` int(11) UNSIGNED DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tblemployeerole`
--

INSERT INTO `tblemployeerole` (`id`, `username`, `company_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', 1, 1, '2019-05-06 17:41:24', '2019-05-06 10:41:24'),
(3, 'ddd', 1, 44, '2019-05-06 10:41:31', '2019-05-06 10:41:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblgaji`
--

CREATE TABLE `tblgaji` (
  `id` int(11) NOT NULL,
  `nip` varchar(100) NOT NULL,
  `gaji` int(11) NOT NULL,
  `tunjangan` int(11) NOT NULL,
  `bonus` int(11) NOT NULL,
  `lembur` int(11) NOT NULL,
  `potongan` int(11) NOT NULL,
  `eom3` int(11) NOT NULL,
  `thp` int(11) NOT NULL,
  `creator` varchar(100) NOT NULL,
  `bulan` int(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `tgs1` double NOT NULL,
  `tgs2` double NOT NULL,
  `hari` int(11) NOT NULL,
  `pengali_lembur` int(11) NOT NULL,
  `nominal_tgs1` int(11) NOT NULL,
  `nominal_tgs2` int(11) NOT NULL,
  `total_bv` int(11) NOT NULL,
  `nominal_lembur` int(11) NOT NULL,
  `additional` float NOT NULL,
  `tgs1_persen` double NOT NULL,
  `tgs2_persen` double NOT NULL,
  `add_persen` double NOT NULL,
  `ttl_persen` double NOT NULL,
  `con` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbljurnal`
--

CREATE TABLE `tbljurnal` (
  `id` int(11) NOT NULL,
  `id_jurnal` int(11) UNSIGNED NOT NULL,
  `AccNo` varchar(100) NOT NULL,
  `AccPos` varchar(100) NOT NULL,
  `Amount` double NOT NULL,
  `company_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `description` varchar(1000) NOT NULL,
  `creator` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  `nama_category` varchar(100) NOT NULL,
  `budget_month` varchar(100) NOT NULL,
  `budget_year` varchar(100) NOT NULL,
  `notes_item` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblkoordinator`
--

CREATE TABLE `tblkoordinator` (
  `id` int(11) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `telp` varchar(100) NOT NULL,
  `ktp` varchar(100) NOT NULL,
  `memberid` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblmanageharga`
--

CREATE TABLE `tblmanageharga` (
  `id` int(11) NOT NULL,
  `month` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `harga_modal` double DEFAULT NULL,
  `harga_distributor` double DEFAULT NULL,
  `creator` varchar(100) DEFAULT NULL,
  `prod_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblmember`
--

CREATE TABLE `tblmember` (
  `id` int(11) NOT NULL,
  `member_id` varchar(100) DEFAULT NULL,
  `koordinator` int(11) UNSIGNED DEFAULT NULL,
  `ktp` varchar(100) NOT NULL,
  `subkor` int(11) UNSIGNED DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `telp` varchar(100) NOT NULL,
  `tmptlhr` text NOT NULL,
  `tgllhr` varchar(100) NOT NULL,
  `ibu` varchar(100) NOT NULL,
  `creator` varchar(100) NOT NULL,
  `status` varchar(3) DEFAULT 'RWH',
  `cetak` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblmodul`
--

CREATE TABLE `tblmodul` (
  `modul_id` varchar(100) NOT NULL,
  `modul_desc` varchar(100) NOT NULL,
  `modul_page` varchar(100) NOT NULL,
  `modul_icon` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tblmodul`
--

INSERT INTO `tblmodul` (`modul_id`, `modul_desc`, `modul_page`, `modul_icon`, `created_at`, `updated_at`) VALUES
('CR', 'Customer Management', '../crm/main_cr.php', 'mdi mdi-human', NULL, NULL),
('FI', 'Finance', '../fi/main_fi.php', 'mdi mdi-finance', NULL, NULL),
('HR', 'Employee', '../hr/main_hr.php', 'mdi mdi-teach', NULL, NULL),
('MB', 'Manage Member', '../mb/main_mb.php', 'mdi mdi-human-greeting', NULL, NULL),
('MM', 'Menu', '../mm/main_mm.php', 'mdi mdi-menu', NULL, NULL),
('PD', 'Product', '../pd/main_pd.php', 'mdi mdi-wallet-giftcard', NULL, NULL),
('PS', 'Point Of Sales', '../ps/main_ps.php', 'mdi mdi-sale', NULL, NULL),
('PU', 'Purchasing', '../pu/main_pu.php', 'mdi mdi-transcribe', NULL, NULL),
('RE', 'Retur', '../re/main_re.php', 'mdi mdi-note-text', NULL, NULL),
('RM', 'Role', '../rm/main_rm.php', 'mdi mdi-timetable', NULL, NULL),
('RPR', 'Reporting', '../rpr/main_rpr.php', 'mdi mdi-note-multiple', NULL, NULL),
('SE', 'Security', '../se/main_se.php', 'mdi mdi-security', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblperusahaan`
--

CREATE TABLE `tblperusahaan` (
  `id` int(11) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `telp` varchar(100) NOT NULL,
  `creator` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblpopayment`
--

CREATE TABLE `tblpopayment` (
  `id` int(11) NOT NULL,
  `trx_id` varchar(100) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_amount` bigint(20) NOT NULL,
  `payment_method` varchar(100) NOT NULL,
  `payment_desc` text NOT NULL,
  `due_date` date NOT NULL,
  `deduct_category` varchar(100) NOT NULL,
  `deduct_amount` int(11) NOT NULL,
  `id_jurnal` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblpotrx`
--

CREATE TABLE `tblpotrx` (
  `trx_id` bigint(20) NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `supplier` varchar(100) NOT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `id_jurnal` int(11) DEFAULT '0',
  `tgl` date DEFAULT NULL,
  `approve` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblpotrxdet`
--

CREATE TABLE `tblpotrxdet` (
  `id` int(11) NOT NULL,
  `trx_id` varchar(100) NOT NULL,
  `prod_id` varchar(100) NOT NULL,
  `qty` bigint(11) NOT NULL,
  `unit` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `price` double DEFAULT NULL,
  `price_dist` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblpricedetail`
--

CREATE TABLE `tblpricedetail` (
  `id` int(11) NOT NULL,
  `kode_produk` varchar(100) NOT NULL,
  `kode_customer` varchar(100) NOT NULL,
  `price` bigint(20) NOT NULL,
  `pv` double NOT NULL,
  `creator` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblproduct`
--

CREATE TABLE `tblproduct` (
  `id` int(11) NOT NULL,
  `company_id` int(11) UNSIGNED NOT NULL,
  `prod_id` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `stock` bigint(20) NOT NULL,
  `price` bigint(20) NOT NULL,
  `supplier` varchar(100) NOT NULL,
  `buy_price` bigint(20) NOT NULL,
  `prod_id_new` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblproducttrx`
--

CREATE TABLE `tblproducttrx` (
  `trx_id` bigint(20) UNSIGNED NOT NULL,
  `trx_date` date NOT NULL,
  `username` varchar(100) NOT NULL,
  `payment` bigint(11) NOT NULL,
  `ttl_harga` bigint(11) NOT NULL,
  `cashier` varchar(100) NOT NULL,
  `customer` varchar(100) NOT NULL,
  `id_jurnal` int(11) UNSIGNED DEFAULT '0',
  `id_jurnal_hpp` int(11) DEFAULT NULL,
  `ongkir` int(11) DEFAULT NULL,
  `approve` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblproducttrxdet`
--

CREATE TABLE `tblproducttrxdet` (
  `id` int(11) NOT NULL,
  `trx_id` bigint(20) UNSIGNED NOT NULL,
  `prod_id` varchar(100) NOT NULL,
  `qty` bigint(11) NOT NULL,
  `unit` varchar(100) NOT NULL,
  `price` bigint(11) NOT NULL,
  `sub_ttl` bigint(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `pv` double DEFAULT NULL,
  `sub_ttl_pv` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblreceivedet`
--

CREATE TABLE `tblreceivedet` (
  `id` int(11) NOT NULL,
  `trx_id` int(11) DEFAULT NULL,
  `prod_id` varchar(100) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `ed` date DEFAULT NULL,
  `creator` varchar(100) DEFAULT NULL,
  `driver` varchar(100) DEFAULT NULL,
  `tgl_receive` date DEFAULT NULL,
  `jurnal_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblreturpb`
--

CREATE TABLE `tblreturpb` (
  `trx_id` int(11) NOT NULL,
  `tgl` date NOT NULL,
  `supplier` int(11) NOT NULL,
  `username` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblreturpbdet`
--

CREATE TABLE `tblreturpbdet` (
  `id` int(11) NOT NULL,
  `trx_id` varchar(100) NOT NULL,
  `prod_id` varchar(100) NOT NULL,
  `qty` int(11) NOT NULL,
  `reason` text NOT NULL,
  `username` varchar(100) NOT NULL,
  `tgl` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblreturpj`
--

CREATE TABLE `tblreturpj` (
  `trx_id` int(11) NOT NULL,
  `tgl` date NOT NULL,
  `customer` int(11) NOT NULL,
  `username` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblreturpjdet`
--

CREATE TABLE `tblreturpjdet` (
  `id` int(11) NOT NULL,
  `trx_id` varchar(100) NOT NULL,
  `prod_id` varchar(100) NOT NULL,
  `qty` int(11) NOT NULL,
  `unit` varchar(100) NOT NULL,
  `reason` text NOT NULL,
  `username` varchar(100) NOT NULL,
  `tgl` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblrole`
--

CREATE TABLE `tblrole` (
  `id` int(11) NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `company_id` int(11) NOT NULL,
  `creator` int(11) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tblrole`
--

INSERT INTO `tblrole` (`id`, `role_name`, `company_id`, `creator`, `created_at`, `updated_at`) VALUES
(1, 'Superadmin', 0, 0, '2019-05-09 05:51:28', '2019-05-09 05:51:28'),
(39, 'Direktur Utama', 6, 0, NULL, '2019-05-06 03:15:04'),
(40, 'Kepala Bagian Keuangan', 6, 0, NULL, '2019-05-06 03:15:04'),
(41, 'Staff', 6, 0, NULL, '2019-05-06 03:15:04'),
(42, 'Manager', 6, 0, NULL, '2019-05-06 03:15:04'),
(43, 'Staff Inactive', 6, 0, NULL, '2019-05-06 03:15:04'),
(44, 'Finance Staff', 6, 0, NULL, '2019-05-06 03:15:04'),
(45, 'Security', 6, 0, NULL, '2019-05-06 03:15:04'),
(46, 'CUK', 1, 15, '2019-05-06 10:53:13', '2019-05-06 10:53:13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblsopayment`
--

CREATE TABLE `tblsopayment` (
  `id` int(11) NOT NULL,
  `trx_id` varchar(100) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_amount` bigint(20) NOT NULL,
  `payment_method` varchar(100) NOT NULL,
  `payment_desc` text NOT NULL,
  `due_date` date NOT NULL,
  `deduct_category` varchar(100) NOT NULL,
  `deduct_amount` int(11) NOT NULL,
  `id_jurnal` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblsubkoordinator`
--

CREATE TABLE `tblsubkoordinator` (
  `id` int(11) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `ktp` varchar(100) NOT NULL,
  `telp` varchar(100) NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `creator` varchar(100) NOT NULL,
  `created` datetime NOT NULL,
  `memberid` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblsubmodul`
--

CREATE TABLE `tblsubmodul` (
  `submodul_id` varchar(100) NOT NULL,
  `submodul_desc` varchar(100) NOT NULL,
  `modul_id` varchar(100) NOT NULL,
  `submodul_page` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tblsubmodul`
--

INSERT INTO `tblsubmodul` (`submodul_id`, `submodul_desc`, `modul_id`, `submodul_page`) VALUES
('CRCS', 'Customer Record', 'CR', 'getHome'),
('FICA', 'Chart Of Account', 'FI', 'getHome'),
('FIJE', 'Jurnal Entry', 'FI', 'getHome'),
('FIPJ', 'Perhitungan Pajak', 'FI', 'getHome'),
('FIPO', 'Purchase Order Payment', 'FI', 'getHome'),
('FIRP', 'Financial Reporting', 'FI', 'getHome'),
('FISO', 'Sales Order Payment', 'FI', 'getHome'),
('HRAT', 'Attendance Management', 'HR', 'getHome'),
('HREO', 'Employee Role Management', 'HR', 'getHome'),
('HRER', 'Employee Management', 'HR', 'employee.index'),
('HRSL', 'Employee Salary', 'HR', 'getHome'),
('MBBN', 'Manage Bonus', 'MB', 'getHome'),
('MBFW', 'Follow Up', 'MB', 'getHome'),
('MBKO', 'Manage Koordinator', 'MB', 'getHome'),
('MBMB', 'Manage Member', 'MB', 'getHome'),
('MBPR', 'Manage Perusahaan', 'MB', 'getHome'),
('MBSB', 'Manage Sub Koordinator', 'MB', 'getHome'),
('MMMM', 'Menu Mapping Setup', 'MM', 'getHome'),
('MMNM', 'New Menu Mapping Setup', 'MM', 'getHome'),
('PDPD', 'Product Management', 'PD', 'getHome'),
('PSPD', 'Sales Transaction', 'PS', 'getHome'),
('PUAV', 'Atur Avail', 'PU', 'getHome'),
('PUPO', 'Purchase Order', 'PU', 'getHome'),
('REPB', 'Retur Pembelian Barang', 'RE', 'getHome'),
('REPJ', 'Retur Penjualan', 'RE', 'getHome'),
('RMRM', 'Role Management', 'RM', 'getHome'),
('RPRB', 'Report Bonus', 'RPR', 'getHome'),
('RPRF', 'Financial Reporting', 'RPR', 'getHome'),
('RPRG', 'Graphic', 'RPR', 'getHome'),
('RPRI', 'Report Barang Indent', 'RPR', 'getHome'),
('RPRS', 'Report Barang In Hand', 'RPR', 'getHome'),
('SESE', 'Cek Kartu ATM', 'SE', 'getHome');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbltopupbonus`
--

CREATE TABLE `tbltopupbonus` (
  `id_bonus` int(11) NOT NULL,
  `no_rek` varchar(100) NOT NULL,
  `tgl` varchar(100) NOT NULL,
  `bulan` int(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `bonus` double NOT NULL,
  `creator` varchar(100) NOT NULL,
  `id_jurnal` int(11) UNSIGNED DEFAULT '0',
  `bank` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `test`
--

CREATE TABLE `test` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `sub_id` varchar(50) DEFAULT NULL,
  `jenis` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `test`
--

INSERT INTO `test` (`id`, `user_id`, `sub_id`, `jenis`) VALUES
(1, 1, 'CR', 'create'),
(2, 1, 'CR', 'update'),
(3, 1, 'CR', 'delete'),
(4, 2, 'CR', 'update');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bankmember`
--
ALTER TABLE `bankmember`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ktp3` (`ktp`),
  ADD KEY `fk_bank` (`bank_id`);

--
-- Indeks untuk tabel `demo_device`
--
ALTER TABLE `demo_device`
  ADD PRIMARY KEY (`sn`);

--
-- Indeks untuk tabel `demo_finger`
--
ALTER TABLE `demo_finger`
  ADD PRIMARY KEY (`user_id`);

--
-- Indeks untuk tabel `demo_log`
--
ALTER TABLE `demo_log`
  ADD PRIMARY KEY (`log_time`),
  ADD KEY `fk_user` (`user_name`);

--
-- Indeks untuk tabel `demo_user`
--
ALTER TABLE `demo_user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `fk_username` (`user_name`);

--
-- Indeks untuk tabel `jenismapping`
--
ALTER TABLE `jenismapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_mapping` (`mapping_id`);

--
-- Indeks untuk tabel `map_purchase`
--
ALTER TABLE `map_purchase`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_username5` (`username`);

--
-- Indeks untuk tabel `menumapping`
--
ALTER TABLE `menumapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_userzz` (`user_id`),
  ADD KEY `fk_subid` (`submodul_id`);

--
-- Indeks untuk tabel `perusahaanmember`
--
ALTER TABLE `perusahaanmember`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ktp` (`ktp`),
  ADD KEY `fk_perusahaan` (`perusahaan_id`);

--
-- Indeks untuk tabel `tblavail`
--
ALTER TABLE `tblavail`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tblbank`
--
ALTER TABLE `tblbank`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tblbonus`
--
ALTER TABLE `tblbonus`
  ADD PRIMARY KEY (`id_bonus`),
  ADD KEY `fk_member` (`member_id`);

--
-- Indeks untuk tabel `tblbonusbayar`
--
ALTER TABLE `tblbonusbayar`
  ADD PRIMARY KEY (`id_bonus`);

--
-- Indeks untuk tabel `tblbonusgagal`
--
ALTER TABLE `tblbonusgagal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_member2` (`member_id`);

--
-- Indeks untuk tabel `tblcoa`
--
ALTER TABLE `tblcoa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `AccNo` (`AccNo`),
  ADD KEY `fk_coagrup` (`grup`),
  ADD KEY `fk_company2` (`company_id`);

--
-- Indeks untuk tabel `tblcoagrup`
--
ALTER TABLE `tblcoagrup`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `grup` (`grup`);

--
-- Indeks untuk tabel `tblcompany`
--
ALTER TABLE `tblcompany`
  ADD PRIMARY KEY (`company_id`),
  ADD UNIQUE KEY `company_email` (`company_email`);

--
-- Indeks untuk tabel `tblcustomernew`
--
ALTER TABLE `tblcustomernew`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tbldatakota`
--
ALTER TABLE `tbldatakota`
  ADD PRIMARY KEY (`kota_id`);

--
-- Indeks untuk tabel `tblemployee`
--
ALTER TABLE `tblemployee`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nip` (`nip`),
  ADD UNIQUE KEY `username` (`username`) USING BTREE;

--
-- Indeks untuk tabel `tblemployeerole`
--
ALTER TABLE `tblemployeerole`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_username2` (`username`),
  ADD KEY `fk_role` (`role_id`),
  ADD KEY `fk_company` (`company_id`);

--
-- Indeks untuk tabel `tblgaji`
--
ALTER TABLE `tblgaji`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tbljurnal`
--
ALTER TABLE `tbljurnal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_jurnal` (`id_jurnal`);

--
-- Indeks untuk tabel `tblkoordinator`
--
ALTER TABLE `tblkoordinator`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tblmanageharga`
--
ALTER TABLE `tblmanageharga`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tblmember`
--
ALTER TABLE `tblmember`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ktp` (`ktp`) USING BTREE,
  ADD UNIQUE KEY `member_id` (`member_id`) USING BTREE,
  ADD KEY `fk_koor` (`koordinator`),
  ADD KEY `fk_sub` (`subkor`);

--
-- Indeks untuk tabel `tblmodul`
--
ALTER TABLE `tblmodul`
  ADD PRIMARY KEY (`modul_id`);

--
-- Indeks untuk tabel `tblperusahaan`
--
ALTER TABLE `tblperusahaan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tblpopayment`
--
ALTER TABLE `tblpopayment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_jurnal3` (`id_jurnal`);

--
-- Indeks untuk tabel `tblpotrx`
--
ALTER TABLE `tblpotrx`
  ADD UNIQUE KEY `trx_id` (`trx_id`);

--
-- Indeks untuk tabel `tblpotrxdet`
--
ALTER TABLE `tblpotrxdet`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indeks untuk tabel `tblpricedetail`
--
ALTER TABLE `tblpricedetail`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tblproduct`
--
ALTER TABLE `tblproduct`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `prod_id` (`prod_id`),
  ADD KEY `fk_company3` (`company_id`);

--
-- Indeks untuk tabel `tblproducttrx`
--
ALTER TABLE `tblproducttrx`
  ADD PRIMARY KEY (`trx_id`),
  ADD KEY `fk_jurnal` (`id_jurnal`);

--
-- Indeks untuk tabel `tblproducttrxdet`
--
ALTER TABLE `tblproducttrxdet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_trx` (`trx_id`),
  ADD KEY `fk_product` (`prod_id`);

--
-- Indeks untuk tabel `tblreceivedet`
--
ALTER TABLE `tblreceivedet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_jurnal5` (`jurnal_id`),
  ADD KEY `fk_product3` (`prod_id`);

--
-- Indeks untuk tabel `tblreturpb`
--
ALTER TABLE `tblreturpb`
  ADD PRIMARY KEY (`trx_id`);

--
-- Indeks untuk tabel `tblreturpbdet`
--
ALTER TABLE `tblreturpbdet`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tblreturpj`
--
ALTER TABLE `tblreturpj`
  ADD PRIMARY KEY (`trx_id`);

--
-- Indeks untuk tabel `tblreturpjdet`
--
ALTER TABLE `tblreturpjdet`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tblrole`
--
ALTER TABLE `tblrole`
  ADD PRIMARY KEY (`id`,`updated_at`);

--
-- Indeks untuk tabel `tblsopayment`
--
ALTER TABLE `tblsopayment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_jurnal2` (`id_jurnal`);

--
-- Indeks untuk tabel `tblsubkoordinator`
--
ALTER TABLE `tblsubkoordinator`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tblsubmodul`
--
ALTER TABLE `tblsubmodul`
  ADD PRIMARY KEY (`submodul_id`),
  ADD KEY `fk_modul` (`modul_id`);

--
-- Indeks untuk tabel `tbltopupbonus`
--
ALTER TABLE `tbltopupbonus`
  ADD PRIMARY KEY (`id_bonus`),
  ADD KEY `fk_jurnal4` (`id_jurnal`);

--
-- Indeks untuk tabel `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bankmember`
--
ALTER TABLE `bankmember`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `demo_user`
--
ALTER TABLE `demo_user`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `jenismapping`
--
ALTER TABLE `jenismapping`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `map_purchase`
--
ALTER TABLE `map_purchase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `menumapping`
--
ALTER TABLE `menumapping`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `perusahaanmember`
--
ALTER TABLE `perusahaanmember`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tblavail`
--
ALTER TABLE `tblavail`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tblbank`
--
ALTER TABLE `tblbank`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `tblbonus`
--
ALTER TABLE `tblbonus`
  MODIFY `id_bonus` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tblbonusbayar`
--
ALTER TABLE `tblbonusbayar`
  MODIFY `id_bonus` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tblbonusgagal`
--
ALTER TABLE `tblbonusgagal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tblcoa`
--
ALTER TABLE `tblcoa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tblcoagrup`
--
ALTER TABLE `tblcoagrup`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tblcompany`
--
ALTER TABLE `tblcompany`
  MODIFY `company_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tblcustomernew`
--
ALTER TABLE `tblcustomernew`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tbldatakota`
--
ALTER TABLE `tbldatakota`
  MODIFY `kota_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tblemployee`
--
ALTER TABLE `tblemployee`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `tblemployeerole`
--
ALTER TABLE `tblemployeerole`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `tblgaji`
--
ALTER TABLE `tblgaji`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tbljurnal`
--
ALTER TABLE `tbljurnal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tblkoordinator`
--
ALTER TABLE `tblkoordinator`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tblmanageharga`
--
ALTER TABLE `tblmanageharga`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tblmember`
--
ALTER TABLE `tblmember`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tblperusahaan`
--
ALTER TABLE `tblperusahaan`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tblpopayment`
--
ALTER TABLE `tblpopayment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tblpotrx`
--
ALTER TABLE `tblpotrx`
  MODIFY `trx_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tblpotrxdet`
--
ALTER TABLE `tblpotrxdet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tblpricedetail`
--
ALTER TABLE `tblpricedetail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tblproduct`
--
ALTER TABLE `tblproduct`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tblproducttrxdet`
--
ALTER TABLE `tblproducttrxdet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tblreceivedet`
--
ALTER TABLE `tblreceivedet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tblreturpbdet`
--
ALTER TABLE `tblreturpbdet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tblreturpjdet`
--
ALTER TABLE `tblreturpjdet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tblrole`
--
ALTER TABLE `tblrole`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT untuk tabel `tblsopayment`
--
ALTER TABLE `tblsopayment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tblsubkoordinator`
--
ALTER TABLE `tblsubkoordinator`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tbltopupbonus`
--
ALTER TABLE `tbltopupbonus`
  MODIFY `id_bonus` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `test`
--
ALTER TABLE `test`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bankmember`
--
ALTER TABLE `bankmember`
  ADD CONSTRAINT `fk_bank` FOREIGN KEY (`bank_id`) REFERENCES `tblbank` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ktp3` FOREIGN KEY (`ktp`) REFERENCES `tblmember` (`ktp`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `demo_finger`
--
ALTER TABLE `demo_finger`
  ADD CONSTRAINT `fk_id` FOREIGN KEY (`user_id`) REFERENCES `demo_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `demo_log`
--
ALTER TABLE `demo_log`
  ADD CONSTRAINT `fk_u` FOREIGN KEY (`user_name`) REFERENCES `demo_user` (`user_name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `demo_user`
--
ALTER TABLE `demo_user`
  ADD CONSTRAINT `fk_username` FOREIGN KEY (`user_name`) REFERENCES `tblemployee` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jenismapping`
--
ALTER TABLE `jenismapping`
  ADD CONSTRAINT `fk_mapping` FOREIGN KEY (`mapping_id`) REFERENCES `menumapping` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `map_purchase`
--
ALTER TABLE `map_purchase`
  ADD CONSTRAINT `fk_username5` FOREIGN KEY (`username`) REFERENCES `tblemployee` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `menumapping`
--
ALTER TABLE `menumapping`
  ADD CONSTRAINT `fk_subid` FOREIGN KEY (`submodul_id`) REFERENCES `tblsubmodul` (`submodul_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_userzz` FOREIGN KEY (`user_id`) REFERENCES `tblemployee` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `perusahaanmember`
--
ALTER TABLE `perusahaanmember`
  ADD CONSTRAINT `fk_ktp` FOREIGN KEY (`ktp`) REFERENCES `tblmember` (`ktp`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_perusahaan` FOREIGN KEY (`perusahaan_id`) REFERENCES `tblperusahaan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tblbonus`
--
ALTER TABLE `tblbonus`
  ADD CONSTRAINT `fk_member` FOREIGN KEY (`member_id`) REFERENCES `tblmember` (`member_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tblbonusgagal`
--
ALTER TABLE `tblbonusgagal`
  ADD CONSTRAINT `fk_member2` FOREIGN KEY (`member_id`) REFERENCES `tblmember` (`member_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tblcoa`
--
ALTER TABLE `tblcoa`
  ADD CONSTRAINT `fk_coagrup` FOREIGN KEY (`grup`) REFERENCES `tblcoagrup` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_company2` FOREIGN KEY (`company_id`) REFERENCES `tblcompany` (`company_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ketidakleluasaan untuk tabel `tblemployeerole`
--
ALTER TABLE `tblemployeerole`
  ADD CONSTRAINT `fk_company` FOREIGN KEY (`company_id`) REFERENCES `tblcompany` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_role` FOREIGN KEY (`role_id`) REFERENCES `tblrole` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_username2` FOREIGN KEY (`username`) REFERENCES `tblemployee` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tblmember`
--
ALTER TABLE `tblmember`
  ADD CONSTRAINT `fk_koor` FOREIGN KEY (`koordinator`) REFERENCES `tblkoordinator` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sub` FOREIGN KEY (`subkor`) REFERENCES `tblsubkoordinator` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tblpopayment`
--
ALTER TABLE `tblpopayment`
  ADD CONSTRAINT `fk_jurnal3` FOREIGN KEY (`id_jurnal`) REFERENCES `tbljurnal` (`id_jurnal`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tblproduct`
--
ALTER TABLE `tblproduct`
  ADD CONSTRAINT `fk_company3` FOREIGN KEY (`company_id`) REFERENCES `tblcompany` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tblproducttrx`
--
ALTER TABLE `tblproducttrx`
  ADD CONSTRAINT `fk_jurnal` FOREIGN KEY (`id_jurnal`) REFERENCES `tbljurnal` (`id_jurnal`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tblproducttrxdet`
--
ALTER TABLE `tblproducttrxdet`
  ADD CONSTRAINT `fk_product` FOREIGN KEY (`prod_id`) REFERENCES `tblproduct` (`prod_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_trx` FOREIGN KEY (`trx_id`) REFERENCES `tblproducttrx` (`trx_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tblreceivedet`
--
ALTER TABLE `tblreceivedet`
  ADD CONSTRAINT `fk_jurnal5` FOREIGN KEY (`jurnal_id`) REFERENCES `tbljurnal` (`id_jurnal`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_product3` FOREIGN KEY (`prod_id`) REFERENCES `tblproduct` (`prod_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tblsopayment`
--
ALTER TABLE `tblsopayment`
  ADD CONSTRAINT `fk_jurnal2` FOREIGN KEY (`id_jurnal`) REFERENCES `tbljurnal` (`id_jurnal`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tblsubmodul`
--
ALTER TABLE `tblsubmodul`
  ADD CONSTRAINT `fk_modul` FOREIGN KEY (`modul_id`) REFERENCES `tblmodul` (`modul_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbltopupbonus`
--
ALTER TABLE `tbltopupbonus`
  ADD CONSTRAINT `fk_jurnal4` FOREIGN KEY (`id_jurnal`) REFERENCES `tbljurnal` (`id_jurnal`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
