<?php

declare(strict_types=1);

namespace Tests\Support;

final class SourceDeclarations
{
    /**
     * @return list<string>
     */
    public static function fromCode(string $source): array
    {
        $tokens = token_get_all($source);
        $declarations = [];
        $namespace = '';
        $count = count($tokens);

        for ($index = 0; $index < $count; $index++) {
            $token = $tokens[$index];

            if (! is_array($token)) {
                continue;
            }

            if ($token[0] === T_NAMESPACE) {
                $namespace = '';

                for ($index++; $index < $count; $index++) {
                    $namespaceToken = $tokens[$index];

                    if (is_string($namespaceToken)) {
                        if ($namespaceToken === ';' || $namespaceToken === '{') {
                            break;
                        }

                        continue;
                    }

                    if (in_array($namespaceToken[0], [T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED, T_NS_SEPARATOR], true)) {
                        $namespace .= $namespaceToken[1];
                    }
                }

                $namespace = trim($namespace, '\\');

                continue;
            }

            if (! in_array($token[0], [T_CLASS, T_ENUM, T_INTERFACE, T_TRAIT], true)) {
                continue;
            }

            for ($nameIndex = $index + 1; $nameIndex < $count; $nameIndex++) {
                $nameToken = $tokens[$nameIndex];

                if (is_string($nameToken)) {
                    break;
                }

                if (in_array($nameToken[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                    continue;
                }

                if ($nameToken[0] === T_STRING) {
                    $declarations[] = $namespace === '' ? $nameToken[1] : $namespace.'\\'.$nameToken[1];
                }

                break;
            }
        }

        sort($declarations);

        return $declarations;
    }
}
