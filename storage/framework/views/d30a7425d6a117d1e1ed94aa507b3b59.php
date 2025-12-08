
<div class="mb-4 position-relative shadow-sm p-1" data-id="<?php echo e($clase->Identificador); ?>">
    <!-- Botón editar arriba a la derecha -->
    <div class="position-absolute top-0 end-0 mt-0 d-flex gap-1">
        
        <button type="button" 
                data-tabla=<?php echo e($tabla); ?> 
                <?php if($clase->Publicacion): ?>
                    data-noticia="1" 
                <?php else: ?>
                    data-banner="Banner" 
                <?php endif; ?>  
                data-id="<?php echo e($clase->Identificador); ?>" 
                data-orden="<?php echo e($clase->Orden); ?>" 
                class="btn btn-sm btn-primary"
                data-bs-toggle="modal" 
                data-bs-target="<?php echo e($clase->Publicacion ? '#noticia' : '#banner'); ?>">
            <i class="mdi mdi-pencil"></i> Editar
        </button>

        <button type="button" 
                data-tabla=<?php echo e($tabla); ?> 

                <?php if($clase->Publicacion): ?>
                    data-eliminar="Noticia" 
                <?php else: ?>
                    data-eliminar="Banner" 
                <?php endif; ?>  
                data-id="<?php echo e($clase->Identificador); ?>" 
                data-orden="<?php echo e($clase->Orden); ?>" 
                class="btn btn-sm btn-danger">
                <i class="mdi mdi-delete"></i>
        </button>
    </div>
    <?php if($clase->Publicacion): ?>
        
        <?php if($tabla == 'portada_izquierda' && $clase->Orden == 1): ?>
            <div class="overflow-hidden" style="background: url('<?php echo e(URL::asset('images/1641266162.jpg')); ?>') center center / cover no-repeat; min-height: 280px;">
              
                <div class="d-flex flex-column justify-content-end h-100 w-100" style="background: rgba(255,255,255,0.0);">
                    <div class="p-3 text-primary" style="background: rgba(255,255,255,0.7); border-radius: 0 0 12px 12px;">
                        <h5 class="text-primary mb-1">Welcome Back !</h5>
                        <p class="mb-0">Skote Dashboard</p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class=" w-100 align-items-center">
                <!-- Imagen de la noticia -->
                <img src="<?php echo e(URL::asset('storage/'.$clase?->publicacion?->imagenes->first()->Imagen)); ?>"
                    alt="Noticia" class="img-fluid rounded  me-3" style="width: 100px; height: 100px; object-fit: cover;">
                <div class="mt-3">
                    <h5 class="mb-2"><?php echo e($clase?->publicacion->Titulo ?? 'Título de la noticia'); ?></h5>
                    <p><?php echo e($clase?->publicacion?->Fecha->format('d/m/Y')); ?> | <?php echo e($clase?->publicacion?->Autor); ?></p>
                </div>
            </div>
        <?php endif; ?>
    <?php elseif($clase->BannerCodigoFuente): ?>
        <?php echo e(Str::limit($clase->BannerCodigoFuente,200)); ?>

    <?php elseif($clase->BannerUrl && !$clase->BannerImagen): ?>
        <?php if(strstr($clase->BannerUrl,"youtube") || strstr($clase->BannerUrl,"youtu.be")): ?>
            <!-- Extraer el ID del video de YouTube -->
            <?php
                preg_match("/(youtu\.be\/|v=)([a-zA-Z0-9_-]{11})/", $clase->BannerUrl, $matches);
                $youtubeId = $matches[2] ?? null;
            ?>
            <?php if($youtubeId): ?>
                <iframe width="100%" height="140"
                    src="https://www.youtube.com/embed/<?php echo e($youtubeId); ?>"
                    title="YouTube video" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
            <?php endif; ?>
        <?php else: ?>
            <?php echo e($clase->BannerUrl); ?>

        <?php endif; ?>
    
    <?php elseif($clase->BannerImagen): ?>
        <img src="<?php echo e(asset('storage/'.$clase->BannerImagen)); ?>" 
            alt="<?php echo e($clase->BannerTitulo); ?>"
            title="<?php echo e($clase->BannerTitulo); ?>" 
            width="<?php echo e(config('gecox_portada.banners.{$clase}.ancho', '1080')); ?>"
            height="<?php echo e(config('gecox_portada.banners.{$clase}.alto', '150')); ?>"
            class="img-fluid">
    <?php endif; ?>
</div><?php /**PATH /Users/curro/Documents/WEBSERVICES/_GECOX2025/resources/views/portada/item.blade.php ENDPATH**/ ?>