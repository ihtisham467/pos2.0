import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Settings\StoreController::edit
* @see app/Http/Controllers/Settings/StoreController.php:19
* @route '/settings/store'
*/
export const edit = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/settings/store',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Settings\StoreController::edit
* @see app/Http/Controllers/Settings/StoreController.php:19
* @route '/settings/store'
*/
edit.url = (options?: RouteQueryOptions) => {
    return edit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\StoreController::edit
* @see app/Http/Controllers/Settings/StoreController.php:19
* @route '/settings/store'
*/
edit.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Settings\StoreController::edit
* @see app/Http/Controllers/Settings/StoreController.php:19
* @route '/settings/store'
*/
edit.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Settings\StoreController::edit
* @see app/Http/Controllers/Settings/StoreController.php:19
* @route '/settings/store'
*/
const editForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Settings\StoreController::edit
* @see app/Http/Controllers/Settings/StoreController.php:19
* @route '/settings/store'
*/
editForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Settings\StoreController::edit
* @see app/Http/Controllers/Settings/StoreController.php:19
* @route '/settings/store'
*/
editForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

edit.form = editForm

/**
* @see \App\Http\Controllers\Settings\StoreController::update
* @see app/Http/Controllers/Settings/StoreController.php:57
* @route '/settings/store'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/settings/store',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Settings\StoreController::update
* @see app/Http/Controllers/Settings/StoreController.php:57
* @route '/settings/store'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\StoreController::update
* @see app/Http/Controllers/Settings/StoreController.php:57
* @route '/settings/store'
*/
update.patch = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Settings\StoreController::update
* @see app/Http/Controllers/Settings/StoreController.php:57
* @route '/settings/store'
*/
const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Settings\StoreController::update
* @see app/Http/Controllers/Settings/StoreController.php:57
* @route '/settings/store'
*/
updateForm.patch = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

const store = {
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
}

export default store