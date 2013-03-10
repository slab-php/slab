<div id="myCarousel" class="carousel slide span8">
    <div class="carousel-inner">
        <?php $first = true; ?>
        <?php foreach ($items as $item): ?>
            <div class="item <?php if ($first): $first = false; ?>active<?php endif; ?>">
                <img src="<?php e($html->url($item['url'])); ?>">
                <div class="carousel-caption">
                    <h4><?php eh($item['label']); ?></h4>
                    <p><?php eh($item['text']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <a class="left carousel-control" href="#myCarousel" data-slide="prev">&lsaquo;</a>
    <a class="right carousel-control" href="#myCarousel" data-slide="next">&rsaquo;</a>
</div>
