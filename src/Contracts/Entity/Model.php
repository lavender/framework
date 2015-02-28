<?php
namespace Lavender\Contracts\Entity;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Model as Eloquent;

interface Model
{


    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = []);

    /**
     * Register a new global scope on the model.
     *
     * @param  \Illuminate\Database\Eloquent\ScopeInterface  $scope
     * @return void
     */
    public static function addGlobalScope(ScopeInterface $scope);

    /**
     * Determine if a model has a global scope.
     *
     * @param  \Illuminate\Database\Eloquent\ScopeInterface  $scope
     * @return bool
     */
    public static function hasGlobalScope($scope);

    /**
     * Get a global scope registered with the model.
     *
     * @param  \Illuminate\Database\Eloquent\ScopeInterface  $scope
     * @return \Illuminate\Database\Eloquent\ScopeInterface|null
     */
    public static function getGlobalScope($scope);

    /**
     * Get the global scopes for this class instance.
     *
     * @return \Illuminate\Database\Eloquent\ScopeInterface[]
     */
    public function getGlobalScopes();

    /**
     * Register an observer with the Model.
     *
     * @param  object  $class
     * @return void
     */
    public static function observe($class);

    /**
     * Fill the model with an array of attributes.
     *
     * @param  array  $attributes
     * @return $this
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function fill(array $attributes);

    /**
     * Fill the model with an array of attributes. Force mass assignment.
     *
     * @param  array  $attributes
     * @return $this
     */
    public function forceFill(array $attributes);

    /**
     * Create a new instance of the given model.
     *
     * @param  array  $attributes
     * @param  bool   $exists
     * @return static
     */
    public function newInstance($attributes = [], $exists = false);

    /**
     * Create a new model instance that is existing.
     *
     * @param  array  $attributes
     * @return static
     */
    public function newFromBuilder($attributes = []);

    /**
     * Create a collection of models from plain arrays.
     *
     * @param  array  $items
     * @param  string  $connection
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function hydrate(array $items, $connection = null);

    /**
     * Create a collection of models from a raw query.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @param  string  $connection
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function hydrateRaw($query, $bindings = [], $connection = null);

    /**
     * Save a new model and return the instance.
     *
     * @param  array  $attributes
     * @return static
     */
    public static function create(array $attributes);

    /**
     * Save a new model and return the instance. Allow mass-assignment.
     *
     * @param  array  $attributes
     * @return static
     */
    public static function forceCreate(array $attributes);

    /**
     * Get the first record matching the attributes or create it.
     *
     * @param  array  $attributes
     * @return static
     */
    public static function firstOrCreate(array $attributes);

    /**
     * Get the first record matching the attributes or instantiate it.
     *
     * @param  array  $attributes
     * @return static
     */
    public static function firstOrNew(array $attributes);

    /**
     * Create or update a record matching the attributes, and fill it with values.
     *
     * @param  array  $attributes
     * @param  array  $values
     * @return static
     */
    public static function updateOrCreate(array $attributes, array $values = []);

    /**
     * Begin querying the model.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function query();

    /**
     * Begin querying the model on a given connection.
     *
     * @param  string  $connection
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function on($connection = null);

    /**
     * Begin querying the model on the write connection.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public static function onWriteConnection();

    /**
     * Get all of the models from the database.
     *
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function all($columns = ['*']);

    /**
     * Find a model by its primary key.
     *
     * @param  mixed  $id
     * @param  array  $columns
     * @return \Illuminate\Support\Collection|static|null
     */
    public static function find($id, $columns = ['*']);

    /**
     * Find a model by its primary key or return new static.
     *
     * @param  mixed  $id
     * @param  array  $columns
     * @return \Illuminate\Support\Collection|static
     */
    public static function findOrNew($id, $columns = ['*']);

    /**
     * Reload a fresh model instance from the database.
     *
     * @param  array  $with
     * @return $this
     */
    public function fresh(array $with = []);

    /**
     * Eager load relations on the model.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function load($relations);

    /**
     * Being querying a model with eager loading.
     *
     * @param  array|string  $relations
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public static function with($relations);

    /**
     * Define a one-to-one relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOne($related, $foreignKey = null, $localKey = null);

    /**
     * Define a polymorphic one-to-one relationship.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function morphOne($related, $name, $type = null, $id = null, $localKey = null);

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $otherKey
     * @param  string  $relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null);

    /**
     * Define a polymorphic, inverse one-to-one or many relationship.
     *
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function morphTo($name = null, $type = null, $id = null);

    /**
     * Define a one-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasMany($related, $foreignKey = null, $localKey = null);

    /**
     * Define a has-many-through relationship.
     *
     * @param  string  $related
     * @param  string  $through
     * @param  string|null  $firstKey
     * @param  string|null  $secondKey
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function hasManyThrough($related, $through, $firstKey = null, $secondKey = null);

    /**
     * Define a polymorphic one-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function morphMany($related, $name, $type = null, $id = null, $localKey = null);

    /**
     * Define a many-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $table
     * @param  string  $foreignKey
     * @param  string  $otherKey
     * @param  string  $relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function belongsToMany($related, $table = null, $foreignKey = null, $otherKey = null, $relation = null);

    /**
     * Define a polymorphic many-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string  $table
     * @param  string  $foreignKey
     * @param  string  $otherKey
     * @param  bool    $inverse
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function morphToMany($related, $name, $table = null, $foreignKey = null, $otherKey = null, $inverse = false);

    /**
     * Define a polymorphic, inverse many-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string  $table
     * @param  string  $foreignKey
     * @param  string  $otherKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function morphedByMany($related, $name, $table = null, $foreignKey = null, $otherKey = null);

    /**
     * Get the joining table name for a many-to-many relation.
     *
     * @param  string  $related
     * @return string
     */
    public function joiningTable($related);

    /**
     * Destroy the models for the given IDs.
     *
     * @param  array|int  $ids
     * @return int
     */
    public static function destroy($ids);

    /**
     * Delete the model from the database.
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete();

    /**
     * Force a hard delete on a soft deleted model.
     *
     * This method protects developers from running forceDelete when trait is missing.
     *
     * @return void
     */
    public function forceDelete();

    /**
     * Register a saving model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @param  int  $priority
     * @return void
     */
    public static function saving($callback, $priority = 0);

    /**
     * Register a saved model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @param  int  $priority
     * @return void
     */
    public static function saved($callback, $priority = 0);

    /**
     * Register an updating model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @param  int  $priority
     * @return void
     */
    public static function updating($callback, $priority = 0);

    /**
     * Register an updated model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @param  int  $priority
     * @return void
     */
    public static function updated($callback, $priority = 0);

    /**
     * Register a creating model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @param  int  $priority
     * @return void
     */
    public static function creating($callback, $priority = 0);

    /**
     * Register a created model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @param  int  $priority
     * @return void
     */
    public static function created($callback, $priority = 0);

    /**
     * Register a deleting model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @param  int  $priority
     * @return void
     */
    public static function deleting($callback, $priority = 0);

    /**
     * Register a deleted model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @param  int  $priority
     * @return void
     */
    public static function deleted($callback, $priority = 0);

    /**
     * Remove all of the event listeners for the model.
     *
     * @return void
     */
    public static function flushEventListeners();

    /**
     * Get the observable event names.
     *
     * @return array
     */
    public function getObservableEvents();

    /**
     * Set the observable event names.
     *
     * @param  array  $observables
     * @return void
     */
    public function setObservableEvents(array $observables);

    /**
     * Add an observable event name.
     *
     * @param  mixed  $observables
     * @return void
     */
    public function addObservableEvents($observables);

    /**
     * Remove an observable event name.
     *
     * @param  mixed  $observables
     * @return void
     */
    public function removeObservableEvents($observables);

    /**
     * Update the model in the database.
     *
     * @param  array  $attributes
     * @return bool|int
     */
    public function update(array $attributes = []);

    /**
     * Save the model and all of its relationships.
     *
     * @return bool
     */
    public function push();

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = []);

    /**
     * Touch the owning relations of the model.
     *
     * @return void
     */
    public function touchOwners();

    /**
     * Determine if the model touches a given relation.
     *
     * @param  string  $relation
     * @return bool
     */
    public function touches($relation);

    /**
     * Update the model's update timestamp.
     *
     * @return bool
     */
    public function touch();

    /**
     * Set the value of the "created at" attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setCreatedAt($value);

    /**
     * Set the value of the "updated at" attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setUpdatedAt($value);

    /**
     * Get the name of the "created at" column.
     *
     * @return string
     */
    public function getCreatedAtColumn();

    /**
     * Get the name of the "updated at" column.
     *
     * @return string
     */
    public function getUpdatedAtColumn();

    /**
     * Get a fresh timestamp for the model.
     *
     * @return \Carbon\Carbon
     */
    public function freshTimestamp();

    /**
     * Get a fresh timestamp for the model.
     *
     * @return string
     */
    public function freshTimestampString();

    /**
     * Get a new query builder for the model's table.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery();

    /**
     * Get a new query instance without a given scope.
     *
     * @param  \Illuminate\Database\Eloquent\ScopeInterface  $scope
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQueryWithoutScope($scope);

    /**
     * Get a new query builder that doesn't have any global scopes.
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newQueryWithoutScopes();

    /**
     * Apply all of the global scopes to an Eloquent builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applyGlobalScopes($builder);

    /**
     * Remove all of the global scopes from an Eloquent builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function removeGlobalScopes($builder);

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query);

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = []);

    /**
     * Create a new pivot model instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  array   $attributes
     * @param  string  $table
     * @param  bool    $exists
     * @return \Illuminate\Database\Eloquent\Relations\Pivot
     */
    public function newPivot(Eloquent $parent, array $attributes, $table, $exists);

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable();

    /**
     * Set the table associated with the model.
     *
     * @param  string  $table
     * @return void
     */
    public function setTable($table);

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey();

    /**
     * Get the queueable identity for the entity.
     *
     * @return mixed
     */
    public function getQueueableId();

    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName();

    /**
     * Set the primary key for the model.
     *
     * @param  string  $key
     * @return void
     */
    public function setKeyName($key);

    /**
     * Get the table qualified key name.
     *
     * @return string
     */
    public function getQualifiedKeyName();

    /**
     * Get the value of the model's route key.
     *
     * @return mixed
     */
    public function getRouteKey();

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName();

    /**
     * Determine if the model uses timestamps.
     *
     * @return bool
     */
    public function usesTimestamps();

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass();

    /**
     * Get the number of models to return per page.
     *
     * @return int
     */
    public function getPerPage();

    /**
     * Set the number of models to return per page.
     *
     * @param  int   $perPage
     * @return void
     */
    public function setPerPage($perPage);

    /**
     * Get the default foreign key name for the model.
     *
     * @return string
     */
    public function getForeignKey();

    /**
     * Get the hidden attributes for the model.
     *
     * @return array
     */
    public function getHidden();

    /**
     * Set the hidden attributes for the model.
     *
     * @param  array  $hidden
     * @return void
     */
    public function setHidden(array $hidden);

    /**
     * Add hidden attributes for the model.
     *
     * @param  array|string|null  $attributes
     * @return void
     */
    public function addHidden($attributes = null);

    /**
     * Get the visible attributes for the model.
     *
     * @return array
     */
    public function getVisible();

    /**
     * Set the visible attributes for the model.
     *
     * @param  array  $visible
     * @return void
     */
    public function setVisible(array $visible);

    /**
     * Add visible attributes for the model.
     *
     * @param  array|string|null  $attributes
     * @return void
     */
    public function addVisible($attributes = null);

    /**
     * Set the accessors to append to model arrays.
     *
     * @param  array  $appends
     * @return void
     */
    public function setAppends(array $appends);

    /**
     * Get the fillable attributes for the model.
     *
     * @return array
     */
    public function getFillable();

    /**
     * Set the fillable attributes for the model.
     *
     * @param  array  $fillable
     * @return $this
     */
    public function fillable(array $fillable);

    /**
     * Get the guarded attributes for the model.
     *
     * @return array
     */
    public function getGuarded();

    /**
     * Set the guarded attributes for the model.
     *
     * @param  array  $guarded
     * @return $this
     */
    public function guard(array $guarded);

    /**
     * Disable all mass assignable restrictions.
     *
     * @return void
     */
    public static function unguard();

    /**
     * Enable the mass assignment restrictions.
     *
     * @return void
     */
    public static function reguard();

    /**
     * Set "unguard" to a given state.
     *
     * @param  bool  $state
     * @return void
     */
    public static function setUnguardState($state);

    /**
     * Determine if the given attribute may be mass assigned.
     *
     * @param  string  $key
     * @return bool
     */
    public function isFillable($key);

    /**
     * Determine if the given key is guarded.
     *
     * @param  string  $key
     * @return bool
     */
    public function isGuarded($key);

    /**
     * Determine if the model is totally guarded.
     *
     * @return bool
     */
    public function totallyGuarded();

    /**
     * Get the relationships that are touched on save.
     *
     * @return array
     */
    public function getTouchedRelations();

    /**
     * Set the relationships that are touched on save.
     *
     * @param  array  $touches
     * @return void
     */
    public function setTouchedRelations(array $touches);

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing();

    /**
     * Set whether IDs are incrementing.
     *
     * @param  bool  $value
     * @return void
     */
    public function setIncrementing($value);

    /**
     * Convert the model instance to JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0);

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize();

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray();

    /**
     * Convert the model's attributes to an array.
     *
     * @return array
     */
    public function attributesToArray();

    /**
     * Get the model's relationships in array form.
     *
     * @return array
     */
    public function relationsToArray();

    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key);

    /**
     * Determine if a get mutator exists for an attribute.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasGetMutator($key);

    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function setAttribute($key, $value);

    /**
     * Determine if a set mutator exists for an attribute.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasSetMutator($key);

    /**
     * Get the attributes that should be converted to dates.
     *
     * @return array
     */
    public function getDates();

    /**
     * Convert a DateTime to a storable string.
     *
     * @param  \DateTime|int  $value
     * @return string
     */
    public function fromDateTime($value);

    /**
     * Clone the model into a new, non-existing instance.
     *
     * @param  array  $except
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function replicate(array $except = null);

    /**
     * Get all of the current attributes on the model.
     *
     * @return array
     */
    public function getAttributes();

    /**
     * Set the array of model attributes. No checking is done.
     *
     * @param  array  $attributes
     * @param  bool   $sync
     * @return void
     */
    public function setRawAttributes(array $attributes, $sync = false);

    /**
     * Get the model's original attribute values.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return array
     */
    public function getOriginal($key = null, $default = null);

    /**
     * Sync the original attributes with the current.
     *
     * @return $this
     */
    public function syncOriginal();

    /**
     * Sync a single original attribute with its current value.
     *
     * @param  string  $attribute
     * @return $this
     */
    public function syncOriginalAttribute($attribute);

    /**
     * Determine if the model or given attribute(s) have been modified.
     *
     * @param  array|string|null  $attributes
     * @return bool
     */
    public function isDirty($attributes = null);

    /**
     * Get the attributes that have been changed since last sync.
     *
     * @return array
     */
    public function getDirty();

    /**
     * Get all the loaded relations for the instance.
     *
     * @return array
     */
    public function getRelations();

    /**
     * Get a specified relationship.
     *
     * @param  string  $relation
     * @return mixed
     */
    public function getRelation($relation);

    /**
     * Set the specific relationship in the model.
     *
     * @param  string  $relation
     * @param  mixed   $value
     * @return $this
     */
    public function setRelation($relation, $value);

    /**
     * Set the entire relations array on the model.
     *
     * @param  array  $relations
     * @return $this
     */
    public function setRelations(array $relations);

    /**
     * Get the database connection for the model.
     *
     * @return \Illuminate\Database\Connection
     */
    public function getConnection();

    /**
     * Get the current connection name for the model.
     *
     * @return string
     */
    public function getConnectionName();

    /**
     * Set the connection associated with the model.
     *
     * @param  string  $name
     * @return $this
     */
    public function setConnection($name);

    /**
     * Resolve a connection instance.
     *
     * @param  string  $connection
     * @return \Illuminate\Database\Connection
     */
    public static function resolveConnection($connection = null);

    /**
     * Get the connection resolver instance.
     *
     * @return \Illuminate\Database\ConnectionResolverInterface
     */
    public static function getConnectionResolver();

    /**
     * Set the connection resolver instance.
     *
     * @param  \Illuminate\Database\ConnectionResolverInterface  $resolver
     * @return void
     */
    public static function setConnectionResolver(Resolver $resolver);

    /**
     * Unset the connection resolver for models.
     *
     * @return void
     */
    public static function unsetConnectionResolver();

    /**
     * Get the event dispatcher instance.
     *
     * @return \Illuminate\Contracts\Events\Dispatcher
     */
    public static function getEventDispatcher();

    /**
     * Set the event dispatcher instance.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $dispatcher
     * @return void
     */
    public static function setEventDispatcher(Dispatcher $dispatcher);

    /**
     * Unset the event dispatcher for models.
     *
     * @return void
     */
    public static function unsetEventDispatcher();

    /**
     * Get the mutated attributes for a given instance.
     *
     * @return array
     */
    public function getMutatedAttributes();

}