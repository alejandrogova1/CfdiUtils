<?php

namespace CfdiUtilsTests\PemPrivateKey;

use CfdiUtils\Certificado\Certificado;
use CfdiUtils\PemPrivateKey\PemPrivateKey;
use CfdiUtilsTests\TestCase;

final class PemPrivateKeyTest extends TestCase
{
    public function providerConstructWithBadArgument(): array
    {
        return [
            'empty' => [''],
            'random content' => ['foo bar'],
            'file://' => ['file://'],
            'file without prefix' => [__FILE__],
            'non-existent-file' => ['file://' . __DIR__ . '/non-existent-file'],
            'existent but is a directory' => ['file://' . __DIR__],
            'existent but invalid file' => ['file://' . __FILE__],
            'cer file' => ['file://' . static::utilAsset('certs/EKU9003173C9.cer')],
            'key not pem file' => ['file://' . static::utilAsset('certs/EKU9003173C9.key')],
            'no footer' => ['-----BEGIN PRIVATE KEY-----XXXXX'],
            'hidden url' => ['file://https://cdn.kernel.org/pub/linux/kernel/v4.x/linux-4.13.9.tar.xz'],
        ];
    }

    /**
     * @param string $key
     * @dataProvider providerConstructWithBadArgument
     */
    public function testConstructWithBadArgument(string $key): void
    {
        $this->expectException(\UnexpectedValueException::class);
        new PemPrivateKey($key);
    }

    public function testConstructWithKeyFile(): void
    {
        $keyfile = $this->utilAsset('certs/EKU9003173C9.key.pem');
        $privateKey = new PemPrivateKey('file://' . $keyfile);
        $this->assertInstanceOf(PemPrivateKey::class, $privateKey);
    }

    public function testConstructWithKeyContents(): void
    {
        $keyfile = $this->utilAsset('certs/EKU9003173C9.key.pem');
        $privateKey = new PemPrivateKey(strval(file_get_contents($keyfile)));
        $this->assertInstanceOf(PemPrivateKey::class, $privateKey);
    }

    public function testOpenAndClose(): void
    {
        $passPhrase = '';
        $keyfile = $this->utilAsset('certs/EKU9003173C9.key.pem');
        $privateKey = new PemPrivateKey(strval(file_get_contents($keyfile)));
        $this->assertFalse($privateKey->isOpen());
        $this->assertTrue($privateKey->open($passPhrase));
        $this->assertTrue($privateKey->isOpen());
        $privateKey->close();
        $this->assertFalse($privateKey->isOpen());
    }

    public function testOpenWithBadKey(): void
    {
        $keyContents = "-----BEGIN PRIVATE KEY-----\nXXXXX\n-----END PRIVATE KEY-----";
        $privateKey = new PemPrivateKey($keyContents);
        $this->assertFalse($privateKey->open(''));
    }

    public function testOpenWithIncorrectPassPhrase(): void
    {
        $passPhrase = 'dummy password';
        $keyfile = $this->utilAsset('certs/EKU9003173C9_password.key.pem');
        $privateKey = new PemPrivateKey(strval(file_get_contents($keyfile)));
        $this->assertFalse($privateKey->open($passPhrase));
        $this->assertFalse($privateKey->isOpen());
    }

    public function testOpenWithCorrectPassPhrase(): void
    {
        $passPhrase = '12345678a';
        $keyfile = $this->utilAsset('certs/EKU9003173C9_password.key.pem');
        $privateKey = new PemPrivateKey(strval(file_get_contents($keyfile)));
        $this->assertTrue($privateKey->open($passPhrase));
        $this->assertTrue($privateKey->isOpen());
    }

    public function testCloneOpenKey(): void
    {
        $keyfile = $this->utilAsset('certs/EKU9003173C9.key.pem');
        $privateKey = new PemPrivateKey(strval(file_get_contents($keyfile)));
        $this->assertTrue($privateKey->open(''));

        $cloned = clone $privateKey;
        $this->assertFalse($cloned->isOpen());
        $this->assertTrue($cloned->open(''));
    }

    public function testSerializeOpenKey(): void
    {
        $keyfile = $this->utilAsset('certs/EKU9003173C9.key.pem');
        $privateKey = new PemPrivateKey(strval(file_get_contents($keyfile)));
        $this->assertTrue($privateKey->open(''));

        /** @var PemPrivateKey $serialized */
        $serialized = unserialize(serialize($privateKey));
        $this->assertFalse($serialized->isOpen());
        $this->assertTrue($serialized->open(''));
    }

    public function testSignWithClosedKey(): void
    {
        $keyfile = $this->utilAsset('certs/EKU9003173C9.key.pem');
        $privateKey = new PemPrivateKey(strval(file_get_contents($keyfile)));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The private key is not open');
        $privateKey->sign('');
    }

    public function testSign(): void
    {
        // this signature was createrd using the following command:
        // echo -n lorem ipsum | openssl dgst -sha256 -sign EKU9003173C9.key.pem -out - | base64 -w 80

        $keyfile = $this->utilAsset('certs/EKU9003173C9.key.pem');
        $privateKey = new PemPrivateKey(strval(file_get_contents($keyfile)));
        $privateKey->open('');

        $content = 'lorem ipsum';
        $expectedSign = <<< EOC
            Dhtz+Ou926kNk0B9iv7MF+8ts2yfeuIJhB7/sfuUqCwbzWnpX9/CxWIWMXZOiF/jBU8tREoTh+claQKD
            wjkyjuaZX47hN7P9fklfxA5Sq258frhm0KQ7kPi9FTFjmTUhcHoc92+z6jfGVfNe8R7OFMnxzWKp03Gy
            IC1ewW0HOpmba445T2rSEyjUKZaClfdxbESkUFCeJbXCLsuE9LxoPiMp7zY+haV254fq2psIjTvt1xd8
            Carv0WG58VC4IPTphedHj2SPb3YbikgxJZnCVu6vzf3MTrydZe65GAxoqaLecVzriQbbV90WMx/lkAT4
            /wCuxjvmHDoghs4JtQdaCA==
            EOC;
        $sign = chunk_split(base64_encode($privateKey->sign($content, OPENSSL_ALGO_SHA256)), 80, "\n");
        $this->assertEquals($expectedSign, rtrim($sign));
    }

    public function testBelongsToWithClosedKey(): void
    {
        $keyfile = $this->utilAsset('certs/EKU9003173C9.key.pem');
        $privateKey = new PemPrivateKey(strval(file_get_contents($keyfile)));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The private key is not open');
        $privateKey->belongsTo('');
    }

    public function testBelongsTo(): void
    {
        $cerfile = $this->utilAsset('certs/EKU9003173C9.cer');
        $keyfile = $this->utilAsset('certs/EKU9003173C9.key.pem');
        $privateKey = new PemPrivateKey(strval(file_get_contents($keyfile)));
        $privateKey->open('');
        $certificado = new Certificado($cerfile);
        $this->assertTrue($privateKey->belongsTo($certificado->getPemContents()));
    }
}
