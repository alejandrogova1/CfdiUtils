<?php

namespace CfdiUtilsTests\OpenSSL;

use CfdiUtils\OpenSSL\PemExtractor;
use CfdiUtilsTests\TestCase;

final class PemExtractorTest extends TestCase
{
    public function testExtractorWithEmptyContent(): void
    {
        $extractor = new PemExtractor('');
        $this->assertSame('', $extractor->getContents());
        $this->assertSame('', $extractor->extractCertificate());
        $this->assertSame('', $extractor->extractPublicKey());
        $this->assertSame('', $extractor->extractCertificate());
    }

    public function providerCrLfAndLf(): array
    {
        return [
            'CRLF' => ["\r\n"],
            'LF' => ["\n"],
        ];
    }

    /**
     * @param string $eol
     * @dataProvider providerCrLfAndLf
     */
    public function testExtractorWithFakeContent(string $eol): void
    {
        // section contents must be base64 valid strings
        $info = str_replace(["\r", "\n"], ['[CR]', '[LF]'], $eol);
        $content = implode($eol, [
            '-----BEGIN OTHER SECTION-----',
            'OTHER SECTION',
            '-----END OTHER SECTION-----',
            '-----BEGIN CERTIFICATE-----',
            'FOO+CERTIFICATE',
            '-----END CERTIFICATE-----',
            '-----BEGIN PUBLIC KEY-----',
            'FOO+PUBLIC+KEY',
            '-----END PUBLIC KEY-----',
            '-----BEGIN PRIVATE KEY-----',
            'FOO+PRIVATE+KEY',
            '-----END PRIVATE KEY-----',
        ]);
        $extractor = new PemExtractor($content);
        $this->assertSame($content, $extractor->getContents());
        $this->assertStringContainsString(
            'FOO+CERTIFICATE',
            $extractor->extractCertificate(),
            "Certificate using EOL $info was not extracted"
        );
        $this->assertStringContainsString(
            'FOO+PUBLIC+KEY',
            $extractor->extractPublicKey(),
            "Public Key using EOL $info was not extracted"
        );
        $this->assertStringContainsString(
            'FOO+PRIVATE+KEY',
            $extractor->extractPrivateKey(),
            "Private Key using EOL $info was not extracted"
        );
    }

    public function testExtractCertificateWithPublicKey(): void
    {
        $pemcerpub = $this->utilAsset('certs/EKU9003173C9.cer.pem');
        $contents = strval(file_get_contents($pemcerpub));

        $extractor = new PemExtractor($contents);
        $this->assertSame($contents, $extractor->getContents());

        $this->assertStringContainsString('PUBLIC KEY', $extractor->extractPublicKey());
        $this->assertStringContainsString('CERTIFICATE', $extractor->extractCertificate());
    }

    public function testExtractPrivateKey(): void
    {
        $pemkey = $this->utilAsset('certs/EKU9003173C9.key.pem');
        $contents = strval(file_get_contents($pemkey));

        $extractor = new PemExtractor($contents);
        $this->assertStringContainsString('PRIVATE KEY', $extractor->extractPrivateKey());
    }

    public function testUsingBinaryFileExtractNothing(): void
    {
        $pemkey = $this->utilAsset('certs/EKU9003173C9.key');
        $contents = strval(file_get_contents($pemkey));

        $extractor = new PemExtractor($contents);

        $this->assertSame('', $extractor->extractCertificate());
        $this->assertSame('', $extractor->extractPublicKey());
        $this->assertSame('', $extractor->extractPrivateKey());
    }
}
