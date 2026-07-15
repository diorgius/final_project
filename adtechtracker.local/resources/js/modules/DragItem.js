/**
 * Класс для drag&drop элементов, что бы все элементы были draggeble, в том числе и созданные через websocket
 */
export default class DragItem {

    static setupItem(item, activeSelector) {

        // устанавливаем свойство
        item.draggable = true;

        item.addEventListener('dragstart', e => {

            e.dataTransfer.setData('id', item.id);

            // получаем статус
            const currentStatus =
                item.closest(activeSelector)
                    ? 'active'
                    : 'deactive';

            e.dataTransfer.setData('oldStatus', currentStatus);
        });
    }
}
