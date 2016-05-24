<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Console;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\CS\Console\Command\FixCommand;

/**
 * @author SpacePossum
 *
 * @internal
 */
final class TextDiffTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $expected
     * @param string $format
     * @param bool   $isDecorated
     *
     * @dataProvider provideDiffReporting
     */
    public function testDiffReportingDecorated($expected, $format, $isDecorated)
    {
        $command = new FixCommand();
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                'path' => __DIR__.'/TextDiffTestInput.test',
                '--fixers' => 'concat_without_spaces',
                '--dry-run' => true,
                '--diff' => true,
                '--format' => $format,
            ),
            array(
                'decorated' => $isDecorated,
                'verbosity' => OutputInterface::VERBOSITY_NORMAL,
            )
        );

        if ($isDecorated !== $commandTester->getOutput()->isDecorated()) {
            $this->markTestSkipped(sprintf('Output should %sbe decorated.', $isDecorated ? '' : 'not '));
        }

        if ($isDecorated !== $commandTester->getOutput()->getFormatter()->isDecorated()) {
            $this->markTestSkipped(sprintf('Formatter should %sbe decorated.', $isDecorated ? '' : 'not '));
        }

        $this->assertStringMatchesFormat($expected, $commandTester->getDisplay(false));
    }

    public function provideDiffReporting()
    {
        $expected = <<<'TEST'
%A$output->writeln('<error>' . 'a' . '</error>');%A
%A$output->writeln('<error>'.'a'.'</error>');%A
TEST;
        $cases = array();
        foreach (array('txt', 'xml') as $format) {
            $cases[] = array($expected, $format, true);
            $cases[] = array($expected, $format, false);
        }

        $expected = substr(json_encode($expected), 1, -1);
        $cases[] = array($expected, 'json', true);
        $cases[] = array($expected, 'json', false);

        return $cases;
    }
}
