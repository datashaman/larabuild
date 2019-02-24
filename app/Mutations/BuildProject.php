<?php

namespace App\Mutations;

use App\Models\Project;
use App\Support\ProjectBuilder;
use GraphQL\Type\Definition\ResolveInfo;
use Log;
use Nuwave\Lighthouse\Exceptions\AuthorizationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class BuildProject
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
        $project = Project::findOrFail($args['id']);

        if (auth()->user()->can('build', $project)) {
            $build = app(ProjectBuilder::class)->build($project, $args['commit']);

            return $build;
        }

        throw new AuthorizationException('You are not authorized to access buildProject');
    }
}
