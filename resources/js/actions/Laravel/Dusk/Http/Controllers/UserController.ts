import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults, validateParameters } from './../../../../../wayfinder'
/**
* @see \Laravel\Dusk\Http\Controllers\UserController::login
* @see vendor/laravel/dusk/src/Http/Controllers/UserController.php:38
* @route '/_dusk/login/{userId}/{guard?}'
*/
export const login = (args: { userId: string | number, guard?: string | number } | [userId: string | number, guard: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(args, options),
    method: 'get',
})

login.definition = {
    methods: ["get","head"],
    url: '/_dusk/login/{userId}/{guard?}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Dusk\Http\Controllers\UserController::login
* @see vendor/laravel/dusk/src/Http/Controllers/UserController.php:38
* @route '/_dusk/login/{userId}/{guard?}'
*/
login.url = (args: { userId: string | number, guard?: string | number } | [userId: string | number, guard: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            userId: args[0],
            guard: args[1],
        }
    }

    args = applyUrlDefaults(args)

    validateParameters(args, [
        "guard",
    ])

    const parsedArgs = {
        userId: args.userId,
        guard: args.guard,
    }

    return login.definition.url
            .replace('{userId}', parsedArgs.userId.toString())
            .replace('{guard?}', parsedArgs.guard?.toString() ?? '')
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Laravel\Dusk\Http\Controllers\UserController::login
* @see vendor/laravel/dusk/src/Http/Controllers/UserController.php:38
* @route '/_dusk/login/{userId}/{guard?}'
*/
login.get = (args: { userId: string | number, guard?: string | number } | [userId: string | number, guard: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Dusk\Http\Controllers\UserController::login
* @see vendor/laravel/dusk/src/Http/Controllers/UserController.php:38
* @route '/_dusk/login/{userId}/{guard?}'
*/
login.head = (args: { userId: string | number, guard?: string | number } | [userId: string | number, guard: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: login.url(args, options),
    method: 'head',
})

/**
* @see \Laravel\Dusk\Http\Controllers\UserController::login
* @see vendor/laravel/dusk/src/Http/Controllers/UserController.php:38
* @route '/_dusk/login/{userId}/{guard?}'
*/
const loginForm = (args: { userId: string | number, guard?: string | number } | [userId: string | number, guard: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: login.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Dusk\Http\Controllers\UserController::login
* @see vendor/laravel/dusk/src/Http/Controllers/UserController.php:38
* @route '/_dusk/login/{userId}/{guard?}'
*/
loginForm.get = (args: { userId: string | number, guard?: string | number } | [userId: string | number, guard: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: login.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Dusk\Http\Controllers\UserController::login
* @see vendor/laravel/dusk/src/Http/Controllers/UserController.php:38
* @route '/_dusk/login/{userId}/{guard?}'
*/
loginForm.head = (args: { userId: string | number, guard?: string | number } | [userId: string | number, guard: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: login.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

login.form = loginForm

/**
* @see \Laravel\Dusk\Http\Controllers\UserController::logout
* @see vendor/laravel/dusk/src/Http/Controllers/UserController.php:59
* @route '/_dusk/logout/{guard?}'
*/
export const logout = (args?: { guard?: string | number } | [guard: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logout.url(args, options),
    method: 'get',
})

logout.definition = {
    methods: ["get","head"],
    url: '/_dusk/logout/{guard?}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Dusk\Http\Controllers\UserController::logout
* @see vendor/laravel/dusk/src/Http/Controllers/UserController.php:59
* @route '/_dusk/logout/{guard?}'
*/
logout.url = (args?: { guard?: string | number } | [guard: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { guard: args }
    }

    if (Array.isArray(args)) {
        args = {
            guard: args[0],
        }
    }

    args = applyUrlDefaults(args)

    validateParameters(args, [
        "guard",
    ])

    const parsedArgs = {
        guard: args?.guard,
    }

    return logout.definition.url
            .replace('{guard?}', parsedArgs.guard?.toString() ?? '')
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Laravel\Dusk\Http\Controllers\UserController::logout
* @see vendor/laravel/dusk/src/Http/Controllers/UserController.php:59
* @route '/_dusk/logout/{guard?}'
*/
logout.get = (args?: { guard?: string | number } | [guard: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logout.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Dusk\Http\Controllers\UserController::logout
* @see vendor/laravel/dusk/src/Http/Controllers/UserController.php:59
* @route '/_dusk/logout/{guard?}'
*/
logout.head = (args?: { guard?: string | number } | [guard: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: logout.url(args, options),
    method: 'head',
})

/**
* @see \Laravel\Dusk\Http\Controllers\UserController::logout
* @see vendor/laravel/dusk/src/Http/Controllers/UserController.php:59
* @route '/_dusk/logout/{guard?}'
*/
const logoutForm = (args?: { guard?: string | number } | [guard: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: logout.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Dusk\Http\Controllers\UserController::logout
* @see vendor/laravel/dusk/src/Http/Controllers/UserController.php:59
* @route '/_dusk/logout/{guard?}'
*/
logoutForm.get = (args?: { guard?: string | number } | [guard: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: logout.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Dusk\Http\Controllers\UserController::logout
* @see vendor/laravel/dusk/src/Http/Controllers/UserController.php:59
* @route '/_dusk/logout/{guard?}'
*/
logoutForm.head = (args?: { guard?: string | number } | [guard: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: logout.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

logout.form = logoutForm

/**
* @see \Laravel\Dusk\Http\Controllers\UserController::user
* @see vendor/laravel/dusk/src/Http/Controllers/UserController.php:17
* @route '/_dusk/user/{guard?}'
*/
export const user = (args?: { guard?: string | number } | [guard: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: user.url(args, options),
    method: 'get',
})

user.definition = {
    methods: ["get","head"],
    url: '/_dusk/user/{guard?}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Dusk\Http\Controllers\UserController::user
* @see vendor/laravel/dusk/src/Http/Controllers/UserController.php:17
* @route '/_dusk/user/{guard?}'
*/
user.url = (args?: { guard?: string | number } | [guard: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { guard: args }
    }

    if (Array.isArray(args)) {
        args = {
            guard: args[0],
        }
    }

    args = applyUrlDefaults(args)

    validateParameters(args, [
        "guard",
    ])

    const parsedArgs = {
        guard: args?.guard,
    }

    return user.definition.url
            .replace('{guard?}', parsedArgs.guard?.toString() ?? '')
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Laravel\Dusk\Http\Controllers\UserController::user
* @see vendor/laravel/dusk/src/Http/Controllers/UserController.php:17
* @route '/_dusk/user/{guard?}'
*/
user.get = (args?: { guard?: string | number } | [guard: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: user.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Dusk\Http\Controllers\UserController::user
* @see vendor/laravel/dusk/src/Http/Controllers/UserController.php:17
* @route '/_dusk/user/{guard?}'
*/
user.head = (args?: { guard?: string | number } | [guard: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: user.url(args, options),
    method: 'head',
})

/**
* @see \Laravel\Dusk\Http\Controllers\UserController::user
* @see vendor/laravel/dusk/src/Http/Controllers/UserController.php:17
* @route '/_dusk/user/{guard?}'
*/
const userForm = (args?: { guard?: string | number } | [guard: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: user.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Dusk\Http\Controllers\UserController::user
* @see vendor/laravel/dusk/src/Http/Controllers/UserController.php:17
* @route '/_dusk/user/{guard?}'
*/
userForm.get = (args?: { guard?: string | number } | [guard: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: user.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Dusk\Http\Controllers\UserController::user
* @see vendor/laravel/dusk/src/Http/Controllers/UserController.php:17
* @route '/_dusk/user/{guard?}'
*/
userForm.head = (args?: { guard?: string | number } | [guard: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: user.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

user.form = userForm

const UserController = { login, logout, user }

export default UserController