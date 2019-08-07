<?php

namespace Webmasters\Doctrine\ORM\Query;

/**
 * FunctionClass to enable random sorting
 *
 * @link https://gist.github.com/Ocramius/919465
 * @author Marco Pivetta
 */
use \Doctrine\ORM\Query\AST\Functions\FunctionNode;
use \Doctrine\ORM\Query\Lexer;
use \Doctrine\ORM\Query\Parser;
use \Doctrine\ORM\Query\SqlWalker;

/**
 * RandFunction ::= "RAND" "(" ")"
 * Class RandFunction
 * @package Webmasters\Doctrine\ORM\Query
 */
class RandFunction extends FunctionNode {
    /**
     * @param Parser $parser
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function parse(Parser $parser): void {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * @param SqlWalker $sqlWalker
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker): string {
        return 'RAND()';
    }
}
