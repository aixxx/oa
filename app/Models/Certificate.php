<?php

namespace App\Models;

use App\Models\Coffer\UserCoffer;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Carbon;
use phpseclib\Crypt\RSA;
use phpseclib\File\X509;
use DevFixException;
/**
 * App\Models\Certificate
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $name
 * @property mixed $key_file
 * @property mixed $crt_file
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Certificate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Certificate whereCrtFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Certificate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Certificate whereKeyFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Certificate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Certificate whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Certificate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Certificate whereUserId($value)
 * @property string $password 私钥密码
 * @property mixed $private_key 私钥
 * @property mixed $public_key 公钥
 * @property mixed $pkcs12
 * @property string $revoked_at 吊销日期
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Certificate wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Certificate wherePkcs12($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Certificate wherePrivateKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Certificate wherePublicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Certificate whereRevokedAt($value)
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Coffer\UserCoffer[] $user_coffers
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Certificate onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Certificate whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Certificate withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Certificate withoutTrashed()
 */
class Certificate extends Model
{
    use SoftDeletes;
    const TYPE_USER   = 'user';
    const TYPE_COFFER = 'coffer';

    const COFFER_REVOKED_DAYS = 7000;
    const USER_REVOKED_DAYS   = 60;

    protected $fillable         = ['user_id', 'type', 'name'];
    protected $hidden           = ['password', 'private_key', 'pkcs12'];
    public    $real_private_key = '';

    public function user_coffers()
    {
        return $this->hasMany(\App\Models\Coffer\UserCoffer::class, 'certificate_id', 'id');
    }

    public function getPrivateKey($password = '')
    {
        if ($this->type == self::TYPE_COFFER) {
            if (!$this->real_private_key) {
                $this->real_private_key = decrypt_with_password($password, $this->private_key);
            }
            return $this->real_private_key;
        }
        return $this->private_key;
    }

    public function setPrivateKey($value, $password = '')
    {
        if ($this->type == self::TYPE_COFFER) {
            $this->real_private_key = $value;
            $this->private_key      = encrypt_with_password($password, $value);
        } else {
            $this->private_key = $value;
        }
        return $this;
    }

    public function getCert($user_id)
    {
        //重新生成新的证书，有效期2个月
        self::userIssue($user_id);
        return Certificate::firstUserCertOrFail($user_id);
    }

    public static function firstUserCert($user_id)
    {
        return $certificate = Certificate::where('user_id', $user_id)->where('type', self::TYPE_USER)->first();
    }

    public static function firstUserCertOrFail($user_id)
    {
        return $certificate = Certificate::where('user_id', $user_id)->where('type', self::TYPE_USER)->firstOrFail();
    }

    public static function firstCofferCert($user_id)
    {
        return $certificate = Certificate::where('user_id', $user_id)->where('type', self::TYPE_COFFER)->first();
    }

    public static function firstCofferCertOrFail($user_id)
    {
        return $certificate = Certificate::where('user_id', $user_id)->where('type', self::TYPE_COFFER)->firstOrFail();
    }

    public static function firstCaCert()
    {
        return Certificate::where('name', 'CA')->where('user_id', 0)->first();
    }

    public static function userIssue($user_id)
    {
        $user   = User::findOrFail($user_id);
        $DEVICE = self::createCert($user, $export_password, $pkcs12, self::USER_REVOKED_DAYS);
        //写入数据库
        $userCert = Certificate::firstUserCert($user_id);
        if (!$userCert) {
            $userCert = Certificate::create([
                'user_id' => $user_id,
                'type'    => self::TYPE_USER,
            ]);
        }
        $userCert->name        = $user->name;
        $userCert->password    = encrypt($export_password);
        $userCert->private_key = $DEVICE["keypair"]["privatekey"];
        $userCert->public_key  = $DEVICE['asciicert'];
        $userCert->pkcs12      = base64_encode($pkcs12);
        $userCert->revoked_at  = Carbon::now()->addDay(self::USER_REVOKED_DAYS - 1);
        $userCert->save();
        return $userCert;
    }

    public static function cofferIssue(User $user, $password)
    {
        $export_password = '';
        $DEVICE          = self::createCert($user, $export_password, $pkcs12, self::COFFER_REVOKED_DAYS);
        //写入数据库
        $userCert = Certificate::firstCofferCert($user->id);
        if (!$userCert) {
            $userCert = Certificate::create([
                'user_id' => $user->id,
                'type'    => self::TYPE_COFFER,
            ]);
        }
        $userCert->name       = $user->name;
        $userCert->password   = $export_password;
        $userCert->public_key = $DEVICE['asciicert'];
        $userCert->pkcs12     = base64_encode($pkcs12);
        $userCert->revoked_at = Carbon::now()->addDay(self::COFFER_REVOKED_DAYS - 1);
        $userCert->setPrivateKey($DEVICE["keypair"]["privatekey"], $password);
        $userCert->save();
        return $userCert;
    }

    private static function createCert(User $user, &$export_password, &$pkcs12, $expires_days)
    {
        $ca = Certificate::firstCaCert();
        if (!$ca) {
            throw new DevFixException('未配置系统ca');
        }
        // Setup our CA
        $CA        = [];      // Store our certificate authority information
        $CA["key"] = new RSA();
        $CA["key"]->loadKey($ca->private_key); // Load our CA key to sign with
        // $CA["key"           ]->setPassword('123456');
        $CA["asciicert"] = $ca->public_key;
        $CA["cert"]      = new X509();
        $CA["cert"]->loadX509($CA["asciicert"]);        // Load our CA cert and public key
        $CA["cert"]->setPrivateKey($CA["key"]);

        // Create a new key pair
        $DEVICE            = [];
        $DEVICE["keys"]    = new RSA();
        $DEVICE["keypair"] = $DEVICE["keys"]->createKey(2048);
        // Save our private key
        $DEVICE["privkey"] = new RSA();
        $DEVICE["privkey"]->loadKey($DEVICE["keypair"]["privatekey"]);

        // Save our public key
        $DEVICE["pubkey"] = new RSA();
        $DEVICE["pubkey"]->loadKey($DEVICE["keypair"]["publickey"]);

        // Create a new CSR
        $DEVICE["csr"] = new X509();
        $DEVICE["csr"]->setPrivateKey($DEVICE["privkey"]);
        $DEVICE["csr"]->setPublicKey($DEVICE["pubkey"]);
        $DEVICE["csr"]->setDNProp('CN', $user->name);
        $DEVICE["csr"]->setDNProp('emailAddress', $user->email);

        // Sign the CSR
        $DEVICE["signedcsr"] = $DEVICE["csr"]->signCSR("sha256WithRSAEncryption");
        $DEVICE["asciicsr"]  = $DEVICE["csr"]->saveCSR($DEVICE["signedcsr"]);

        // CSR attributes
        $DEVICE["cert"] = new X509();
        $DEVICE["cert"]->loadCSR($DEVICE["asciicsr"]);          // Now load it back up so we can set extended attributes
        $DEVICE["cert"]->setPublicKey($DEVICE["pubkey"]);
        $DEVICE["cert"]->setStartDate("-1 day");                  // Make it valid from yesterday...
        $DEVICE["cert"]->setEndDate("+ $expires_days days");                 // Set a 5 year expiration on all device certs
        $DEVICE["cert"]->setSerialNumber($user->id, 10);     // Use our ID number in the DB, base 10 (decimal) notation

        //CA sign the updated CSRc
        $DEVICE["signedcert"] = $DEVICE["cert"]->sign($CA["cert"], $DEVICE["cert"], "sha256WithRSAEncryption"); // Sign the new certificate with our CA

        //继续签名？
        $DEVICE["cert"]->loadX509($DEVICE["signedcert"]);
        $DEVICE["cert"]->setExtension("id-ce-keyUsage", ["digitalSignature"], 1);
        $DEVICE["cert"]->setExtension("id-ce-extKeyUsage", ["id-kp-clientAuth"], 1);

        $DEVICE["resignedcert"] = $DEVICE["cert"]->sign($CA["cert"], $DEVICE["cert"], 'sha256WithRSAEncryption');
        $DEVICE["asciicert"]    = $DEVICE["cert"]->saveX509($DEVICE["resignedcert"]);   // Ascii our certificate for presentation

        $export_password = is_null($export_password) ? substr(md5(time() . mt_rand()), 6, 6) : $export_password;
        openssl_pkcs12_export($DEVICE['asciicert'], $pkcs12, $DEVICE["keypair"]["privatekey"], $export_password);
        return $DEVICE;
    }

    public function getCrl()
    {
        $users = User::where('status', User::STATUS_LEAVE)->get()->pluck('id')->toArray();
        return $this->revoke($users);
    }

    /**
     * 从注销用户列表中读取所有需要吊销用户的id，一习性生成
     */
    public function revoke($user_id)
    {
        $ca_file = Certificate::firstCaCert();

        // Load the CA and its private key.
        $cakey = new RSA();
        $cakey->loadKey($ca_file->privete_key);

        //crt file
        $ca = new X509();
        $ca->loadX509($ca_file->public_key);
        $ca->setPrivateKey($cakey);

        // Build the (empty) certificate revocation list.
        $crl = new X509();
        $crl->loadCRL($crl->saveCRL($crl->signCRL($ca, $crl)));

        // Revoke a certificate.
        if (is_array($user_id)) {
            foreach ($user_id as $id) {
                $crl->revoke($id);
            }
        } else {
            $crl->revoke($user_id);
        }

        // Sign the CRL.
        $crl->setSerialNumber(1, 10);

        $crl->setEndDate('+3 months');
        $newcrl = $crl->signCRL($ca, $crl);

        $r = $crl->listRevoked();
        // Output it.
        return $crl->saveCRL($newcrl);

    }
}
