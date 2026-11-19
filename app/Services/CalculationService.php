<?php

namespace App\Services;

class CalculationService
{
    private const FUNCTIONS = ['suma', 'promedio', 'redondeo', 'min', 'max', 'van', 'tir'];

    private string $expr = '';
    private int $pos = 0;

    // Reemplaza cada {{operación}} por su resultado; si no se puede calcular, queda sin llaves y va a "errors"
    public function resolve(string $text): array
    {
        $calculations = [];
        $errors = [];

        $resolved = preg_replace_callback('/\{\{(.*?)\}\}/su', function ($match) use (&$calculations, &$errors) {
            $expression = trim($match[1]);

            try {
                $result = $this->format($this->evaluate($expression), $this->requestedDecimals($expression));
                $calculations[] = ['expression' => $expression, 'result' => $result];

                return $result;
            } catch (\Throwable $e) {
                $errors[] = $expression;

                return $expression;
            }
        }, $text);

        return ['text' => $resolved ?? $text, 'calculations' => $calculations, 'errors' => $errors];
    }

    // Resuelve las marcas en todos los textos de un arreglo (por ejemplo, la respuesta JSON de la IA)
    public function resolveArray(array $data): array
    {
        $calculations = [];
        $errors = [];

        array_walk_recursive($data, function (&$value) use (&$calculations, &$errors) {
            if (is_string($value) && str_contains($value, '{{')) {
                $result = $this->resolve($value);
                $value = $result['text'];
                $calculations = [...$calculations, ...$result['calculations']];
                $errors = [...$errors, ...$result['errors']];
            }
        });

        return ['data' => $data, 'calculations' => $calculations, 'errors' => $errors];
    }

    public function evaluateFormula(string $formula): ?float
    {
        $formula = str_replace(['{{', '}}'], '', $formula);
        $formula = preg_replace('/\bS\/\.?/iu', '', $formula);
        // Quita las palabras, salvo los nombres de función seguidos de "("
        $formula = preg_replace_callback('/\p{L}[\p{L}_]*/u', function ($match) use ($formula) {
            return in_array(mb_strtolower($match[0]), self::FUNCTIONS, true) ? $match[0] : ' ';
        }, $formula);

        // Si no queda ninguna operación, no es una fórmula
        if (!preg_match('/\d\s*[-+*\/×÷^%(]|[a-z]\(/iu', $formula)) {
            return null;
        }

        try {
            return $this->evaluate($formula);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function evaluate(string $expression): float
    {
        $this->expr = str_replace(['×', '÷', '−', '–', ' ', "\u{00A0}"], ['*', '/', '-', '-', '', ''], mb_strtolower($expression));
        $this->pos = 0;

        if ($this->expr === '') {
            throw new \InvalidArgumentException('Expresión vacía');
        }

        $value = $this->parseExpression();

        if ($this->pos < strlen($this->expr)) {
            throw new \InvalidArgumentException("Carácter no válido en: {$expression}");
        }

        if (!is_finite($value)) {
            throw new \InvalidArgumentException("Resultado no válido en: {$expression}");
        }

        return $value;
    }

    public function format(float $value, ?int $decimals = null): string
    {
        if ($decimals === null) {
            $decimals = abs($value - round($value)) < 1e-9 ? 0 : 2;
        }

        $formatted = number_format($value, $decimals, '.', ',');

        return $formatted === '-0' ? '0' : $formatted;
    }

    // Si toda la operación es redondeo(x, n), el resultado se escribe con n decimales
    private function requestedDecimals(string $expression): ?int
    {
        if (preg_match('/^\s*redondeo\s*\((.*),\s*(\d+)\s*\)\s*$/iu', $expression, $match)) {
            return (int) $match[2];
        }

        return null;
    }

    // expresión := término (("+" | "-") término)*
    private function parseExpression(): float
    {
        $value = $this->parseTerm();

        while (($char = $this->peek()) === '+' || $char === '-') {
            $this->pos++;
            $right = $this->parseTerm();
            $value = $char === '+' ? $value + $right : $value - $right;
        }

        return $value;
    }

    // término := potencia (("*" | "/") potencia)*
    private function parseTerm(): float
    {
        $value = $this->parsePower();

        while (($char = $this->peek()) === '*' || $char === '/') {
            $this->pos++;
            $right = $this->parsePower();

            if ($char === '/' && abs($right) < 1e-12) {
                throw new \InvalidArgumentException('División entre cero');
            }

            $value = $char === '*' ? $value * $right : $value / $right;
        }

        return $value;
    }

    // potencia := unario ("^" potencia)?
    private function parsePower(): float
    {
        $base = $this->parseUnary();

        if ($this->peek() === '^') {
            $this->pos++;

            return $base ** $this->parsePower();
        }

        return $base;
    }

    // unario := ("-" | "+") unario | primario "%"*
    private function parseUnary(): float
    {
        $char = $this->peek();

        if ($char === '-' || $char === '+') {
            $this->pos++;
            $value = $this->parseUnary();

            return $char === '-' ? -$value : $value;
        }

        $value = $this->parsePrimary();

        while ($this->peek() === '%') {
            $this->pos++;
            $value /= 100;
        }

        return $value;
    }

    // primario := número | función "(" argumentos ")" | "(" expresión ")"
    private function parsePrimary(): float
    {
        $char = $this->peek();

        if ($char === '(') {
            $this->pos++;
            $value = $this->parseExpression();
            $this->expect(')');

            return $value;
        }

        if ($char !== null && (ctype_digit($char) || $char === '.')) {
            preg_match('/\d*\.?\d+|\d+\.?/A', $this->expr, $match, 0, $this->pos);
            $this->pos += strlen($match[0]);

            return (float) $match[0];
        }

        if (preg_match('/[a-z]+/A', $this->expr, $match, 0, $this->pos)) {
            $name = $match[0];

            if (!in_array($name, self::FUNCTIONS, true)) {
                throw new \InvalidArgumentException("Función desconocida: {$name}");
            }

            $this->pos += strlen($name);
            $this->expect('(');
            $args = [$this->parseExpression()];

            while ($this->peek() === ',' || $this->peek() === ';') {
                $this->pos++;
                $args[] = $this->parseExpression();
            }

            $this->expect(')');

            return $this->callFunction($name, $args);
        }

        throw new \InvalidArgumentException('Expresión incompleta');
    }

    private function callFunction(string $name, array $args): float
    {
        return match ($name) {
            'suma' => array_sum($args),
            'promedio' => array_sum($args) / count($args),
            'redondeo' => round($args[0], (int) ($args[1] ?? 0)),
            'min' => min($args),
            'max' => max($args),
            'van' => $this->npv($args),
            'tir' => $this->irr($args),
        };
    }

    // van(tasa, inversión, flujo1, flujo2…) = -inversión + Σ flujo_t / (1 + tasa)^t
    private function npv(array $args): float
    {
        if (count($args) < 3) {
            throw new \InvalidArgumentException('van necesita tasa, inversión y al menos un flujo');
        }

        [$rate, $investment] = $args;

        return $this->npvAt($rate, $investment, array_slice($args, 2));
    }

    private function npvAt(float $rate, float $investment, array $flows): float
    {
        $value = -abs($investment);

        foreach ($flows as $t => $flow) {
            $value += $flow / ((1 + $rate) ** ($t + 1));
        }

        return $value;
    }

    // tir(inversión, flujo1, flujo2…): la tasa con la que el VAN es cero (por bisección)
    private function irr(array $args): float
    {
        if (count($args) < 2) {
            throw new \InvalidArgumentException('tir necesita inversión y al menos un flujo');
        }

        $investment = array_shift($args);
        $low = -0.99;
        $high = 10.0;

        $npvLow = $this->npvAt($low, $investment, $args);
        $npvHigh = $this->npvAt($high, $investment, $args);

        if ($npvLow * $npvHigh > 0) {
            throw new \InvalidArgumentException('La TIR no existe para estos flujos');
        }

        for ($i = 0; $i < 200; $i++) {
            $mid = ($low + $high) / 2;
            $npvMid = $this->npvAt($mid, $investment, $args);

            if (abs($npvMid) < 1e-7) {
                break;
            }

            if ($npvLow * $npvMid < 0) {
                $high = $mid;
            } else {
                $low = $mid;
                $npvLow = $npvMid;
            }
        }

        return $mid;
    }

    private function peek(): ?string
    {
        return $this->expr[$this->pos] ?? null;
    }

    private function expect(string $char): void
    {
        if ($this->peek() !== $char) {
            throw new \InvalidArgumentException("Se esperaba \"{$char}\"");
        }

        $this->pos++;
    }
}
