<?php

namespace supercrafter333\PlotRate\Commands;

use MyPlot\forms\MyPlotForm;
use MyPlot\MyPlot;
use MyPlot\Plot;
use MyPlot\subcommand\SubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use supercrafter333\PlotRate\PlotRate;

/**
 * Class RateCommand
 * @package supercrafter333\PlotRate\Commands
 */
class RateCommand extends SubCommand
{

    /**
     * @var PlotRate
     */
    private $realPlugin;

    /**
     * RateCommand constructor.
     * @param PlotRate $realPlugin
     */
    public function __construct(PlotRate $realPlugin){
        parent::__construct(MyPlot::getInstance(), $this->getName());
        $this->realPlugin = $realPlugin;
    }

    /**
     * @return PlotRate
     */
    public function getRealPlugin(): PlotRate
    {
        return $this->realPlugin;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'rate';
    }

    /**
     * @param CommandSender|Player $s
     * @return bool
     */
    public function canUse(CommandSender $s): bool
    {
        return ($s instanceof Player) and $s->hasPermission("plotrate.rate.cmd");
    }

    /**
     * @param Player|null $player
     * @return MyPlotForm|null
     */
    public function getForm(?Player $player = null): ?MyPlotForm
    {
        return null;
    }

    /**
     * @param CommandSender|Player $s
     * @param string[] $args
     * @return bool
     */
    public function execute(CommandSender $s, array $args): bool
    {
        if (empty($args[0])) {
            $s->sendMessage("§4Use: §r/p rate <rating: 0-5>");
            return true;
        }
        if ((int)$args[0] < 0 || (int)$args[0] > 5) {
            $s->sendMessage("§4Use: §r/p rate <rating: 0-5>");
            return true;
        }
        $plot = MyPlot::getInstance()->getPlotByPosition($s->getPosition());
        if ($plot instanceof Plot) {
            PlotRate::getInstance()->ratePlot($plot, (int)$args[0]);
            $s->sendMessage(str_replace("{rating}", (string)$args[0], PlotRate::getInstance()->getConfig()->get("rated")));
        } else {
            $s->sendMessage(PlotRate::getInstance()->getConfig()->get("not-in-plot"));
        }
        return true;
    }
}