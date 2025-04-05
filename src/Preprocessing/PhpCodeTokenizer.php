<?php

namespace App\Preprocessing;

use Rubix\ML\Transformers\Transformer;
use Rubix\ML\DataType;

/**
 * PHP Code Tokenizer for preprocessing PHP and Laravel code samples.
 */
class PhpCodeTokenizer implements Transformer
{
    /**
     * Fit the transformer to a dataset.
     *
     * @param array $samples
     * @return void
     */
    public function fit(array $samples = []): void
    {
        // This transformer doesn't need to be fitted
    }

    /**
     * Transform the dataset in place.
     *
     * @param array $samples
     * @return void
     */
    public function transform(array &$samples): void
    {
        foreach ($samples as &$sample) {
            if (is_string($sample)) {
                // Tokenize PHP code
                $tokens = token_get_all($sample);
                
                // Convert tokens to a more usable format
                $processedTokens = [];
                foreach ($tokens as $token) {
                    if (is_array($token)) {
                        list($id, $text) = $token;
                        $processedTokens[] = [
                            'type' => token_name($id),
                            'text' => $text
                        ];
                    } else {
                        $processedTokens[] = [
                            'type' => 'T_CHAR',
                            'text' => $token
                        ];
                    }
                }
                
                // Replace the original code with the tokenized version
                $sample = json_encode($processedTokens);
            }
        }
    }

    /**
     * Return the data types that this transformer is compatible with.
     *
     * @return \Rubix\ML\DataType[]
     */
    public function compatibility(): array
    {
        return [
            DataType::string(),
        ];
    }

    /**
     * Return the string representation of the transformer.
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'PHP Code Tokenizer';
    }
}
