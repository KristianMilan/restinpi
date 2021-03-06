/**
 * Storage manager
 */
angular.module('reeska.storage', [])

/**
 * Data Manager
 */
.factory('$storage', function ($rootScope) {
    var service = {
        model: {
        },

        /**
         * Save data to local storage
         */
        SaveState: function () {
            localStorage.reeskaStorage = angular.toJson(service.model);
        },

        /**
         * Restore data from storage
         */
        RestoreState: function () {
        	if (localStorage.reeskaStorage)
            	service.model = angular.fromJson(localStorage.reeskaStorage);
        },
        
        /**
         * Get stored value, or initialize this with value if not exists.
         */
        get: function(name, value) {
            if (!service.model[name])
                service.model[name] = value;
                
            return service.model[name]
        }
    }

    $rootScope.$on("savestate", service.SaveState);
    $rootScope.$on("restorestate", service.RestoreState);

	/**
	 * Restore data from storage
	 */
    service.RestoreState();

    /**
     * Save data to storage when leaving
     */
    $(window).unload(service.SaveState);

    return service;
});