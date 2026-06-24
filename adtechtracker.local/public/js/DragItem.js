/**
 * Класс для drag&drop элементов, что все элементы были draggeble, в том числе и созданные через websocket
 */
// export default class DragItem {
class DragItem {

    static setupItem(item, activeSelector) {

        item.draggable = true;

        item.addEventListener('dragstart', e => {

            e.dataTransfer.setData('id', item.id);

            const currentStatus =
                item.closest(activeSelector)
                    ? 'active'
                    : 'deactive';

            e.dataTransfer.setData('oldStatus', currentStatus);
        });
    }
}

window.DragItem = DragItem;