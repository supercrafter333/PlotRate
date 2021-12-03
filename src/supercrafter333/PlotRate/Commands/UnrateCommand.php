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
 * Class UnrateCommand
 * @package supercrafter333\PlotRate\Commands
 */
class UnrateCommand extends SubCommand
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
        return 'unrate';
    }

    /**
     * @param CommandSender|Player $s
     * @return bool
     */
    public function canUse(CommandSender|Player $s): bool
    {
        return ($s instanceof Player) and $s->hasPermission("plotrate.unrate.cmd");
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
    public function execute(CommandSender|Player $s, array $args): bool
    {
        $plot = MyPlot::getInstance()->getPlotByPosition($s->getPosition());
        if ($plot instanceof Plot) {
            if (!PlotRate::getInstance()->isRated($plot)) {
                $s->sendMessage(PlotRate::getInstance()->getConfig()->get("not-rated"));
                return true;
            }
            PlotRate::getInstance()->unratePlot($plot);
            $s->sendMessage(PlotRate::getInstance()->getConfig()->get("unrated"));
            return true;
        } else {
            $s->sendMessage(PlotRate::getInstance()->getConfig()->get("not-in-plot"));
            return true;
        }
    }
}