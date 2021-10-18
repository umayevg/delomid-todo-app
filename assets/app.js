/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';
import axios from "axios";

deleteElements()
changeTaskStatus()

function deleteElements(selector) {
    const todosContainer = document.querySelector('#todos-container')
    if (!todosContainer) return

    todosContainer.addEventListener('click', ev => {

        // pour supprimer la Todo
        // if (ev.target.classList.contains('js-todo-delete')) {
        //     ev.preventDefault()
        //     const todo_id = parseInt(ev.target.dataset.todoid)
        //     const todo_token = ev.target.dataset.token
        //
        //     axios.post('/profile/delete-ajax-todo', {
        //         todo_id: todo_id,
        //         _token: todo_token
        //     })
        //         .then(function (response) {
        //             if (response.data.success) {
        //                 ev.target.closest('.todo-col').remove()
        //                 console.log(ev.target.closest('.todo-col'))
        //             }
        //             console.log(response.data.success);
        //         })
        //         .catch(function (error) {
        //             console.log(error);
        //         });
        // }
        removelElement('js-todo-delete', '/profile/delete-ajax-todo', ev, '.todo-col')



        // pour supprimer la Task
        // if (ev.target.classList.contains('js-task-delete')) {
        //     ev.preventDefault()
        //     const task_id = parseInt(ev.target.dataset.taskid)
        //     const task_token = ev.target.dataset.token
        //     console.log(task_id, task_token)
        //
        //     axios.post('/profile/task-delete-ajax', {
        //         task_id: task_id,
        //         _token: task_token
        //     })
        //         .then(function (response) {
        //             if (response.data.success) {
        //                 ev.target.closest('li').remove()
        //                 console.log(ev.target.closest('li'))
        //             }
        //             console.log(response.data);
        //         })
        //         .catch(function (error) {
        //             console.log(error);
        //         });
        // }

        removelElement('js-task-delete', '/profile/task-delete-ajax', ev, 'li')
    })
}

function removelElement(className, url, event, elemToDelete) {
    if (event.target.classList.contains(className)) {
        event.preventDefault()
        const element_id = parseInt(event.target.dataset.id)
        const element_token = event.target.dataset.token
        console.log(element_id, element_token)

        axios.post(url, {
            element: element_id,
            _token: element_token
        })
            .then(function (response) {
                if (response.data.success) {
                    event.target.closest(elemToDelete).remove()
                    console.log(event.target.closest('li'))
                }
                console.log(response.data);
            })
            .catch(function (error) {
                console.log(error);
            });
    }
}

function changeTaskStatus() {
    const todosContainer = document.querySelector('#todos-container')
    if (!todosContainer) return

    todosContainer.addEventListener('click', ev => {
        if (ev.target.classList.contains('task-status')) {
            const token = ev.target.dataset.token
            const element = parseInt(ev.target.dataset.id)
            const status = ev.target.checked
            console.log(token, element, status)


            axios.post('/profile/task-status-change-ajax', {
                element,
                _token: token,
                status
            })
                .then(function (response) {
                    if (response.data.success) {
                        let textColor = ev.target.checked === true ? '#146c43' : 'black'
                        ev.target.closest('li').style.color = textColor
                    }
                    console.log(response.data);
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    })
}