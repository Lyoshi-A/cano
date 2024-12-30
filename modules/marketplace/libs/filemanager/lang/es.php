<?php

return [
    'Select' => 'Seleccionar',
    'Deselect_All' => 'Deseleccionar todos',
    'Select_All' => 'Seleccionar todos',
    'Erase' => 'Eliminar',
    'Open' => 'Abrir',
    'Confirm_del' => '¿Seguro que deseas eliminar este archivo?',
    'All' => 'Todos',
    'Files' => 'Archivos',
    'Images' => 'Imágenes',
    'Archives' => 'Ficheros',
    'Error_Upload' => 'El archivo que intenta subir excede el máximo permitido.',
    'Error_extension' => 'La extensión del archivo no está permitida.',
    'Upload_file' => 'Subir',
    'Filters' => 'Filtros',
    'Videos' => 'Vídeos',
    'Music' => 'Música',
    'New_Folder' => 'Nueva carpeta',
    'Folder_Created' => 'La carpeta ha sido creada correctamente.',
    'Existing_Folder' => 'Carpeta existente',
    'Confirm_Folder_del' => '¿Seguro que deseas eliminar la carpeta y todos los elementos que contiene?',
    'Return_Files_List' => 'Regresar a la lista de archivos',
    'Preview' => 'Vista previa',
    'Download' => 'Descargar',
    'Insert_Folder_Name' => 'Nombre de la carpeta:',
    'Root' => 'raíz',
    'Rename' => 'Renombrar',
    'Back' => 'Atrás',
    'View' => 'Vista',
    'View_list' => 'Vista de lista',
    'View_columns_list' => 'Vista de columnas',
    'View_boxes' => 'Vista de miniaturas',
    'Toolbar' => 'Barra de herramientas',
    'Actions' => 'Acciones',
    'Rename_existing_file' => 'El archivo ya existe',
    'Rename_existing_folder' => 'La carpeta ya existe',
    'Empty_name' => 'El nombre se encuentra vacío',
    'Text_filter' => 'filtro de texto',
    'Swipe_help' => 'Deslize el nombre del archivo/carpeta para mostrar las opciones',
    'Upload_base' => 'Subida de archivos SIMPLE',
    'Upload_base_help' => "Arrastrar y soltar archivos(Drag & Drop Navegadores modernos) o haz click en el botón superior para Añadir los archivos y click en Empezar subida. Cuando la subida se haya completado, haz click en el botón 'Regresar a la lista de archivos'",
    'Upload_add_files' => 'Añadir archivos',
    'Upload_start' => 'Empezar subida',
    'Upload_error_messages' => [
        1 => 'El archivo subido excede la directiva upload_max_filesize en php.ini',
        2 => 'El archivo subido excede la directiva MAX_FILE_SIZE especificada en el formulario HTML',
        3 => 'El archivo subido solo fue subido parcialmente',
        4 => 'No se ha subido ninún archivo',
        6 => 'No se encuentra la carpeta temporal',
        7 => 'Falló la escritura del archivo en el disco',
        8 => 'Una extensión de PHP detuvo la subida del archivo',
        'post_max_size' => 'El archivo subido excede la directiva upload_max_filesize en php.ini',
        'max_file_size' => 'El archivo es demasiado grande',
        'min_file_size' => 'El archivo es demasiado pequeño',
        'accept_file_types' => 'Tipo de archivo (Filetype) no permitido',
        'max_number_of_files' => 'Número máximo de archivos excedido',
        'max_width' => 'La imagen excede el ancho máximo',
        'min_width' => 'La imagen requiere un ancho mínimo',
        'max_height' => 'La imagen excede el alto máximo',
        'min_height' => 'La imagen requiere un alto mínimo',
        'abort' => 'Subida de archivo cancelada',
        'image_resize' => 'Error al redimensionar la imagen',
    ],
    'Upload_url' => 'Desde url',
    'Type_dir' => 'Carpeta',
    'Type' => 'Tipo',
    'Dimension' => 'Dimensiones',
    'Size' => 'Peso',
    'Date' => 'Fecha',
    'Filename' => 'Nombre',
    'Operations' => 'Operaciones',
    'Date_type' => 'y-m-d',
    'OK' => 'Aceptar',
    'Cancel' => 'Cancelar',
    'Sorting' => 'Ordenar',
    'Show_url' => 'Mostrar URL',
    'Extract' => 'Extraer aquí',
    'File_info' => 'Información',
    'Edit_image' => 'Editar imagen',
    'Duplicate' => 'Duplicar',
    'Folders' => 'Carpetas',
    'Copy' => 'Copiar',
    'Cut' => 'Cortar',
    'Paste' => 'Pegar',
    'CB' => 'Portapapeles', // clipboard
    'Paste_Here' => 'Pegar en esta carpeta',
    'Paste_Confirm' => '¿Está seguro de pegar el contenido en esta carpeta? Esta acción sobreescribirá los archivos y carpetas existentes.',
    'Paste_Failed' => 'Error al pegar los archivos',
    'Clear_Clipboard' => 'Limpiar el portapapeles',
    'Clear_Clipboard_Confirm' => '¿Está seguro que desea limpiar el portapapeles?',
    'Files_ON_Clipboard' => 'Existen archivos en el portapapeles',
    'Copy_Cut_Size_Limit' => 'Los archivos/carpetas seleccionados son demasiado grandes para %s. Límite: %d MB/operación', // %s = cut or copy
    'Copy_Cut_Count_Limit' => 'Ha seleccionado demasiados archivos/carpetas para %s. Límite: %d archivos/operación', // %s = cut or copy
    'Copy_Cut_Not_Allowed' => 'No está permitido de %s archivos.', // %s(1) = cut or copy, %s(2) = files or folders
    'Aviary_No_Save' => 'No fue posible guardar la imagen',
    'Zip_No_Extract' => 'No fue posible extraer los archivos. Es posible que el archivo esté corrupto.',
    'Zip_Invalid' => 'Esta extensión no es soportada. Extensiones válidas: zip, gz, tar.',
    'Dir_No_Write' => 'El directorio que ha seleccionado no tiene permisos de escritura.',
    'Function_Disabled' => 'La función %s ha sido deshabilitada en el servidor.', // %s = cut or copy
    'File_Permission' => 'Permisos de archivos',
    'File_Permission_Not_Allowed' => 'Cambiar %s permisos no está permitido.', // %s = files or folders
    'File_Permission_Recursive' => 'Aplicar recursivamente?',
    'File_Permission_Wrong_Mode' => 'El modo de permiso suministrado es incorrecto.',
    'User' => 'Usuario',
    'Group' => 'Grupo',
    'Yes' => 'Si',
    'No' => 'No',
    'Lang_Not_Found' => 'No se ha podido encontrar el idioma.',
    'Lang_Change' => 'Cambiar idioma',
    'File_Not_Found' => 'No se puede encontrar el archivo.',
    'File_Open_Edit_Not_Allowed' => 'No estás autorizado a %s este archivo.', // %s = open or edit
    'Edit' => 'Editar',
    'Edit_File' => 'Editar contenido del archivo',
    'File_Save_OK' => 'Archivo guardado satisfactoriamente.',
    'File_Save_Error' => 'Ocurrió un error guardando el archivo.',
    'New_File' => 'Nuevo archivo',
    'No_Extension' => 'Debes añadir una extensión al archivo.',
    'Valid_Extensions' => 'Extensiones válidas: %s', // %s = txt,log etc.
    'Upload_message' => 'Arrastra archivos aquí para subir',

    'SERVER ERROR' => 'ERROR DEL SERVIDOR',
    'forbiden' => 'Prohibido',
    'wrong path' => 'Ruta incorrecta',
    'wrong name' => 'Nombre incorrecto',
    'wrong extension' => 'Extensión incorrecta',
    'wrong option' => 'Opción incorrecta',
    'wrong data' => 'Datos incorrectos',
    'wrong action' => 'Acción incorrecta',
    'wrong sub-action' => 'Sub-acción incorrecta',
    'no action passed' => 'No se ha recibido una acción',
    'no path' => 'Sin ruta',
    'no file' => 'Sin archivo',
    'view type number missing' => 'Falta el número de tipo de vista',
    'Not enough Memory' => 'No hay memória suficiente',
    'max_size_reached' => 'La carpeta de imágenes ha excedido el tamaño máximo de %d MB.', // %d = max overall size
    'B' => 'B',
    'KB' => 'KB',
    'MB' => 'MB',
    'GB' => 'GB',
    'TB' => 'TB',
    'total size' => 'Tamaño total',
];
