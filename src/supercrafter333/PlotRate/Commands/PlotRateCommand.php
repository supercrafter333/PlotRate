<?php

namespace supercrafter333\PlotRate\Commands;

use MyPlot\MyPlot;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use supercrafter333\PlotRate\PlotRate;

/**
 * Class PlotRateCommand
 * @package supercrafter333\PlotRate\Commands
 */
class PlotRateCommand extends Command implements PluginOwned
{

    /**
     * @var string[]
     */
    private $subCmds = ["help" => "Open the help page.", "a/rand/random" => "Teleport you to a random players plot.", "info" => "Get more informations about PlotRate."];

    /**
     * PlotRateCommand constructor.
     * @param string $name
     * @param string $description
     * @param string $usageMessage
     * @param array|string[] $aliases
     */
    public function __construct(string $name = "plotrate", string $description = "PlotRate -> A plugin to rate your plots!", string $usageMessage = "§4Usage: §r/plotrate help", array $aliases = ["pr"])
    {
        $this->setPermission("plotrate.plotrate.cmd");
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    /**
     * @param CommandSender|Player $s
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $s, string $commandLabel, array $args): void
    {
        $pl = PlotRate::getInstance();
        $cfg = $pl->getConfig();
        if (empty($args[0])) {
            $s->sendMessage($this->usageMessage);
            return;
        }
        if (!$this->testPermission($s)) return;
        $subCmd = array_shift($args);
        switch ($subCmd) {
            case "help":
                $s->sendMessage("§eHelp list of PlotRate: \n");
                foreach ($this->subCmds as $subCmd => $desc) {
                    $s->sendMessage("§7/plotrate $subCmd §b- §8$desc");
                }
                $s->sendMessage("\n§e-------------------------");
                break;
            case "a":
            case "rand":
            case "random":
                if (!$s instanceof Player) {
                    $s->sendMessage($cfg->get("only-IG"));
                    return;
                }
                $matches = [];
                foreach ($pl->getServer()->getOnlinePlayers() as $onlinePlayer) {
                    $plot = MyPlot::getInstance()->getPlotByPosition($onlinePlayer->getPosition());
                    if ($plot !== null) {
                        if ($plot->owner === $onlinePlayer->getName())
                        $matches[] = $onlinePlayer->getName();
                    }
                }
                $matchCount = count($matches, COUNT_RECURSIVE);
                if ($matchCount <= 0) {
                    $s->sendMessage($cfg->get("pr-a-noMatches"));
                    return;
                }
                $XplayerName = array_rand($matches);
                $playerName = $matches[$XplayerName];
                $player = $pl->getServer()->getPlayerByPrefix($playerName);
                $plot = MyPlot::getInstance()->getPlotByPosition($player->getPosition());
                $s->teleport($player->getPosition());
                $s->sendMessage(str_replace(["{plot}", "{owner}"], [(string)$plot->X . ';' . (string)$plot->Z, $plot->owner], $cfg->get("pr-a-success")));
                return;
                break;
            case "info":
                $s->sendMessage("§eInformations of PlotRate: §r\n\n§7Made by: §rsupercrafter333\n§7Helpers: §r---\n§7Icon by: §rShxGux\n§7License: §rApache 2.0 License\n§7GitHub: §rhttps://github.com/supercrafter333/PlotRate\n§7Poggit: §rhttps://poggit.pmmp.io/p/PlotRate\n\n§e-----------------------");
                return;
                break;
        }
    }

    /**
     * @return Plugin
     */
    public function getOwningPlugin(): Plugin
    {
        return PlotRate::getInstance();
    }
}