<?php

namespace App\Mutations;

use App\Models\Project;
use App\Models\Team;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Exceptions\AuthorizationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateProject
{
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function resolve($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = auth()->user();
        $team = Team::findOrFail(Arr::get($args, 'project.teamId'));

        if ($user->hasRole('ADMIN') || $user->hasRole('TEAM_ADMIN', $team)) {
            Arr::set($args, 'project.private_key', Arr::pull($args, 'project.privateKey'));
            Arr::set($args, 'project.team_id', Arr::pull($args, 'project.teamId'));
            return Project::create($args['project']);
        }

        throw new AuthorizationException('You are not authorized to access createProject');
    }
}
