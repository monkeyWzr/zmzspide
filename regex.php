<?php


class Regex{
    /**
     * 匹配ed2k链接
     */
    const ED2K_LINK = '|.*<a\shref="(ed2k:.*)"\stype="ed2k".*<\/a>|sU';
    /**
     * 匹配磁力链接
     */
    const MAGNET_LINK = '|.*<a\shref="(magnet:.*)"\stype="magnet".*<\/a>|sU';
}

