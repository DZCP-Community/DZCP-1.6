<?php
/**
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2013 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use phpseclib3\Crypt\RSA;
use phpseclib3\Math\BigInteger;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\RSA\Formats\Keys\PKCS8;

class Unit_Crypt_RSA_ModeTest extends PhpseclibTestCase
{
    public function testEncryptionModeNone()
    {
        $plaintext = 'a';

        $privatekey = '-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQCqGKukO1De7zhZj6+H0qtjTkVxwTCpvKe4eCZ0FPqri0cb2JZfXJ/DgYSF6vUp
wmJG8wVQZKjeGcjDOL5UlsuusFncCzWBQ7RKNUSesmQRMSGkVb1/3j+skZ6UtW+5u09lHNsj6tQ5
1s1SPrCBkedbNf0Tp0GbMJDyR4e9T04ZZwIDAQABAoGAFijko56+qGyN8M0RVyaRAXz++xTqHBLh
3tx4VgMtrQ+WEgCjhoTwo23KMBAuJGSYnRmoBZM3lMfTKevIkAidPExvYCdm5dYq3XToLkkLv5L2
pIIVOFMDG+KESnAFV7l2c+cnzRMW0+b6f8mR1CJzZuxVLL6Q02fvLi55/mbSYxECQQDeAw6fiIQX
GukBI4eMZZt4nscy2o12KyYner3VpoeE+Np2q+Z3pvAMd/aNzQ/W9WaI+NRfcxUJrmfPwIGm63il
AkEAxCL5HQb2bQr4ByorcMWm/hEP2MZzROV73yF41hPsRC9m66KrheO9HPTJuo3/9s5p+sqGxOlF
L0NDt4SkosjgGwJAFklyR1uZ/wPJjj611cdBcztlPdqoxssQGnh85BzCj/u3WqBpE2vjvyyvyI5k
X6zk7S0ljKtt2jny2+00VsBerQJBAJGC1Mg5Oydo5NwD6BiROrPxGo2bpTbu/fhrT8ebHkTz2epl
U9VQQSQzY1oZMVX8i1m5WUTLPz2yLJIBQVdXqhMCQBGoiuSoSjafUhV7i1cEGpb88h5NBYZzWXGZ
37sJ5QsW+sJyoNde3xH8vdXhzU7eT82D6X/scw9RZz+/6rCJ4p0=
-----END RSA PRIVATE KEY-----';
        $rsa = PublicKeyLoader::load($privatekey);
        $rsa = $rsa->getPublicKey()
            ->withPadding(RSA::ENCRYPTION_NONE);

        $expected = '105b92f59a87a8ad4da52c128b8c99491790ef5a54770119e0819060032fb9e772ed6772828329567f3d7e9472154c1530f8156ba7fd732f52ca1c06' .
            '5a3f5ed8a96c442e4662e0464c97f133aed31262170201993085a589565d67cc9e727e0d087e3b225c8965203b271e38a499c92fc0d6502297eca712' .
            '4d04bd467f6f1e7c';
        $expected = pack('H*', $expected);
        $result = $rsa->encrypt($plaintext);

        $this->assertEquals($result, $expected);

        $rsa = PublicKeyLoader::load($privatekey)
            ->withPadding(RSA::ENCRYPTION_NONE);
        $this->assertEquals(trim($rsa->decrypt($result), "\0"), $plaintext);
    }

    /**
     * @group github768
     */
    public function testPSSSigs()
    {
        $rsa = PublicKeyLoader::load('-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCqGKukO1De7zhZj6+H0qtjTkVx
wTCpvKe4eCZ0FPqri0cb2JZfXJ/DgYSF6vUpwmJG8wVQZKjeGcjDOL5UlsuusFnc
CzWBQ7RKNUSesmQRMSGkVb1/3j+skZ6UtW+5u09lHNsj6tQ51s1SPrCBkedbNf0T
p0GbMJDyR4e9T04ZZwIDAQAB
-----END PUBLIC KEY-----')
            ->withHash('sha1')
            ->withMGFHash('sha1');

        $sig = pack('H*', '1bd29a1d704a906cd7f726370ce1c63d8fb7b9a620871a05f3141a311c0d6e75fefb5d36dfb50d3ea2d37cd67992471419bfadd35da6e13b494' .
            '058ddc9b568d4cfea13ddc3c62b86a6256f5f296980d1131d3eaec6089069a3de79983f73eae20198a18721338b4a66e9cfe80e4f8e4fcef7a5bead5cbb' .
            'b8ac4c76adffbc178c');

        $this->assertTrue($rsa->verify('zzzz', $sig));
    }

    public function testSmallModulo()
    {
        $this->expectException('LengthException');

        $plaintext = 'x';

        $key = PKCS8::savePublicKey(
            new BigInteger(base64_decode('272435F22706FA96DE26E980D22DFF67'), 256), // n
            new BigInteger(base64_decode('158753FF2AF4D1E5BBAB574D5AE6B54D'), 256)  // e
        );
        $rsa = PublicKeyLoader::load($key);

        $rsa->encrypt($plaintext);
    }

    public function testPKCS1LooseVerify()
    {
        $rsa = PublicKeyLoader::load('-----BEGIN RSA PUBLIC KEY-----
MIGJAoGBAMuqkz8ij+ESAaNvgocVGmapjlrIldmhRo4h2NX4e6IXiCLTSxASQtY4
iqRnmyxqQSfaan2okTfQ6sP95bl8Qz8lgneW3ClC6RXG/wpJgsx7TXQ2kodlcKBF
m4k72G75QXhZ+I40ZG7cjBf1/9egakR0a0X0MpeOrKCzMBLv9+mpAgMBAAE=
-----END RSA PUBLIC KEY-----')
            ->withPadding(RSA::SIGNATURE_RELAXED_PKCS1);

        $message = base64_decode('MYIBLjAYBgkqhkiG9w0BCQMxCwYJKoZIhvcNAQcBMBwGCSqGSIb3DQEJBTEPFw0xNDA1MTUxNDM4MzRaMC8GCSqGSIb3DQEJBDEiBCBLzLIBGdOf0L2WRrIY' .
            '9KTwiHnReBW48S9C7LNRaPp5mDCBwgYLKoZIhvcNAQkQAi8xgbIwga8wgawwgakEIJDB9ZGwihf+TaiwrHQNkNHkqbN8Nuws0e77QNObkvFZMIGEMHCkbjBs' .
            'MQswCQYDVQQGEwJJVDEYMBYGA1UECgwPQXJ1YmFQRUMgUy5wLkEuMSEwHwYDVQQLDBhDZXJ0aWZpY2F0aW9uIEF1dGhvcml0eUMxIDAeBgNVBAMMF0FydWJh' .
            'UEVDIFMucC5BLiBORyBDQSAzAhAv4L3QcFssQNLDYN/Vu40R');

        $sig = base64_decode('XDSZWw6IcUj8ICxRJf04HzF8stzoiFAZSR2a0Rw3ziZxTOT0/NVUYJO5+9TaaREXEgxuCLpgmA+6W2SWrrGoxbbNfaI90ZoKeOAws4IX+9RfiWuooibjKcvt' .
            'GJYVVOCcjvQYxUUNbQ4EjCUonk3h7ECXfCCmWqbeq2LsyXeeYGE=');

        $this->assertTrue($rsa->verify($message, $sig));
    }

    public function testZeroLengthSalt()
    {
        $plaintext = 'a';

        $rsa = PublicKeyLoader::load('-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQCqGKukO1De7zhZj6+H0qtjTkVxwTCpvKe4eCZ0FPqri0cb2JZfXJ/DgYSF6vUp
wmJG8wVQZKjeGcjDOL5UlsuusFncCzWBQ7RKNUSesmQRMSGkVb1/3j+skZ6UtW+5u09lHNsj6tQ5
1s1SPrCBkedbNf0Tp0GbMJDyR4e9T04ZZwIDAQABAoGAFijko56+qGyN8M0RVyaRAXz++xTqHBLh
3tx4VgMtrQ+WEgCjhoTwo23KMBAuJGSYnRmoBZM3lMfTKevIkAidPExvYCdm5dYq3XToLkkLv5L2
pIIVOFMDG+KESnAFV7l2c+cnzRMW0+b6f8mR1CJzZuxVLL6Q02fvLi55/mbSYxECQQDeAw6fiIQX
GukBI4eMZZt4nscy2o12KyYner3VpoeE+Np2q+Z3pvAMd/aNzQ/W9WaI+NRfcxUJrmfPwIGm63il
AkEAxCL5HQb2bQr4ByorcMWm/hEP2MZzROV73yF41hPsRC9m66KrheO9HPTJuo3/9s5p+sqGxOlF
L0NDt4SkosjgGwJAFklyR1uZ/wPJjj611cdBcztlPdqoxssQGnh85BzCj/u3WqBpE2vjvyyvyI5k
X6zk7S0ljKtt2jny2+00VsBerQJBAJGC1Mg5Oydo5NwD6BiROrPxGo2bpTbu/fhrT8ebHkTz2epl
U9VQQSQzY1oZMVX8i1m5WUTLPz2yLJIBQVdXqhMCQBGoiuSoSjafUhV7i1cEGpb88h5NBYZzWXGZ
37sJ5QsW+sJyoNde3xH8vdXhzU7eT82D6X/scw9RZz+/6rCJ4p0=
-----END RSA PRIVATE KEY-----')
            ->withSaltLength(0)
            ->withHash('sha1')
            ->withMGFHash('sha1');

        // Check we generate the correct signature.
        $sig = pack('H*', '0ddfc93548e21d015c0a289a640b3b79aecfdfae045f583c5925b91cc5c399bba181616ad6ae20d9662d966f0eb2fddb550f4733268e34d640f4c9dadcaf25b3c82c42130a5081c6ebad7883331c65b25b6a37ffa7c4233a468dae56180787e2718ed87c48d8d50b72f5850e4a40963b4f36710be250ecef6fe0bb91249261a3');
        $this->assertEquals($sig, $rsa->sign($plaintext));

        // Check we can verify the signature correctly.
        $rsa = $rsa->getPublicKey();
        $this->assertTrue($rsa->verify($plaintext, $sig));
    }

    /**
     * @group github1423
     */
    public function testPSSSigsWithNonPowerOf2Key()
    {
        $pub = <<<HERE
-----BEGIN PUBLIC KEY-----
MF0wDQYJKoZIhvcNAQEBBQADTAAwSQJCAmdYuOvii3I6ya3q/zSeZFoJprgF9fIq
k12yS6pCS3c+1wZ9cYFVtgfpSL4XpylLe9EnRT2GRVYCqUkR4AUeTuvnAgMBAAE=
-----END PUBLIC KEY-----
HERE;

        $rsa = PublicKeyLoader::load($pub)
            ->withHash('sha256')
            ->withSaltLength(32)
            ->withMGFHash('sha256');

        $sig = base64_decode(strtr('Ad022bD-UCmWpBNMtsYJjG0FVxML-FFlN4IKrByP8rwjVzV_D-YqSjc_oW6LrooV7jbtEF5803YLn8lllyzDnw00', '-_', '+/'));

        $payload = 'eyJraWQiOiJ0RkMyVUloRnBUTV9FYTNxY09kX01xUVQxY0JCbTlrRkxTRGZlSmhzUkc4IiwiYWxnIjoiUFMyNTYifQ.eyJhcHAiOiJhY2NvdW50cG9ydGFsIiwic3ViIjoiNTliOGM4YzA5NTVhNDA5MDg2MGRmYmM3ZGQwMjVjZWEiLCJjbGlkIjoiZTQ5ZTA2N2JiMTFjNDcyMmEzNGIyYjNiOGE2YTYzNTUiLCJhbSI6InBhc3N3b3JkIiwicCI6ImVOcDFrRUZQd3pBTWhmXC9QdEVOYU5kQkc2bUZDNHNpbENNNXU0aTNXMHFSS0hFVDU5V1JzcXpZRUp4XC84M3ZQbkIxcUg3Rm5CZVNabEtNME9saGVZVUVWTXlHOEVUOEZnWDI4dkdqWG4wWkcrV2hSK01rWVBicGZacHI2U3E0N0RFYjBLYkRFT21CSUZuOTZKN1ZDaWg1Q2p4dWNRZDJmdHJlMCt2cSthZFFObUluK0poWEl0UlBvQ0xya1wvZ05VV3N3T09vSVwva0Q5ZVk4c05jRHFPUzNkanFWb3RPU21oRUo5b0hZZmFqZmpSRzFGSWpGRFwvOExtT2pKbVF3d0tBMnQ0aXJBQ2NncHo0dzBuN3BtXC84YXV2T0dFM2twVFZ2d0IzdzlQZk1YZnJJUTBhejRsaEtIdVBUMU42XC9sb1FJPSIsImlhaSI6IjU5YjhjOGMwOTU1YTQwOTA4NjBkZmJjN2RkMDI1Y2VhIiwiY2xzdmMiOiJhY2NvdW50cG9ydGFsIiwibHB2IjoxNTQ3Njc1NDM4LCJ0IjoicyIsImljIjp0cnVlLCJleHAiOjE1NDc3MDQyMzgsImlhdCI6MTU0NzY3NTQzOCwianRpIjoiZTE0N2UzM2UzNzVhNDkyNWJjMzdjZTRjMDIwMmJjNDYifQ';
        $this->assertTrue($rsa->verify($payload, $sig));
    }

    public function testHash()
    {
        $pub = <<<HERE
-----BEGIN PUBLIC KEY-----
MF0wDQYJKoZIhvcNAQEBBQADTAAwSQJCAmdYuOvii3I6ya3q/zSeZFoJprgF9fIq
k12yS6pCS3c+1wZ9cYFVtgfpSL4XpylLe9EnRT2GRVYCqUkR4AUeTuvnAgMBAAE=
-----END PUBLIC KEY-----
HERE;

        $rsa = PublicKeyLoader::load($pub)
            ->withHash('sha1')
            ->withSaltLength(5)
            ->withMGFHash('sha512');

        $this->assertEquals('sha1', $rsa->getHash());
        $this->assertSame(5, $rsa->getSaltLength());
        $this->assertEquals('sha512', $rsa->getMGFHash());

        $rsa = $rsa
            ->withHash('sha512')
            ->withSaltLength(6)
            ->withMGFHash('sha1');

        $this->assertEquals('sha512', $rsa->getHash());
        $this->assertSame(6, $rsa->getSaltLength());
        $this->assertEquals('sha1', $rsa->getMGFHash());
    }
}
