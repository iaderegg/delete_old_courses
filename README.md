## Moodle module: automated course deletion with CRON
  Adapted to Universidad del Valle Moodle platform

## Modulo de Moodle: borrado de cursos automático con CRON
  Adaptado para la plataforma Moodle de la Universidad del Valle

  ### Versión 0.2
  + Se añade validación al finalizar el script.
  
  ### Versión 0.2.1
  + Envío de correo electrónico de notificación, si al finalizar el proceso quedan cursos pendientes por borrar.
  + Se modifica validación de hora para finalización del script de borrado. Se fija a las 2:00.
  + Se modifica texto donde se indica la hora de inicio del borrado de cursos.

  ### Versión 0.3 (20200529)
  + Se crea libreria para gestión de notificaciones por correo electrónico.
  + Se envia un correo electrónico cada vez que se ejecute el script, notificando cuandos cursos quedan pendientes por eliminar.
  + Se configura validación para la finalización del script a las 01:00 horas y a las 07:00 horas.
