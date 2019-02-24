# Schema

<details>
  <summary><strong>Table of Contents</strong></summary>

  * [Query](#query)
  * [Mutation](#mutation)
  * [Objects](#objects)
    * [Build](#build)
    * [BuildPaginator](#buildpaginator)
    * [PageInfo](#pageinfo)
    * [PaginatorInfo](#paginatorinfo)
    * [Project](#project)
    * [ProjectPaginator](#projectpaginator)
    * [Team](#team)
    * [TeamPaginator](#teampaginator)
    * [User](#user)
    * [UserPaginator](#userpaginator)
    * [UserRole](#userrole)
  * [Inputs](#inputs)
    * [CreateProjectInput](#createprojectinput)
    * [TeamInput](#teaminput)
    * [UpdateProjectInput](#updateprojectinput)
    * [UserInput](#userinput)
  * [Scalars](#scalars)
    * [Boolean](#boolean)
    * [Date](#date)
    * [DateTime](#datetime)
    * [Float](#float)
    * [ID](#id)
    * [Int](#int)
    * [String](#string)
  * [Interfaces](#interfaces)
    * [Node](#node)

</details>

## Query
<table>
<thead>
<tr>
<th align="left">Field</th>
<th align="right">Argument</th>
<th align="left">Type</th>
<th align="left">Description</th>
</tr>
</thead>
<tbody>
<tr>
<td colspan="2" valign="top"><strong>builds</strong></td>
<td valign="top"><a href="#buildpaginator">BuildPaginator</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">count</td>
<td valign="top"><a href="#int">Int</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">page</td>
<td valign="top"><a href="#int">Int</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>build</strong></td>
<td valign="top"><a href="#build">Build</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">id</td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>me</strong></td>
<td valign="top"><a href="#user">User</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>myTeams</strong></td>
<td valign="top">[<a href="#team">Team</a>!]!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>project</strong></td>
<td valign="top"><a href="#project">Project</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">id</td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>projects</strong></td>
<td valign="top"><a href="#projectpaginator">ProjectPaginator</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">count</td>
<td valign="top"><a href="#int">Int</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">page</td>
<td valign="top"><a href="#int">Int</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>projectBuilds</strong></td>
<td valign="top">[<a href="#build">Build</a>!]</td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">project_id</td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>team</strong></td>
<td valign="top"><a href="#team">Team</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">id</td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>teams</strong></td>
<td valign="top"><a href="#teampaginator">TeamPaginator</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">count</td>
<td valign="top"><a href="#int">Int</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">page</td>
<td valign="top"><a href="#int">Int</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>teamProjects</strong></td>
<td valign="top">[<a href="#project">Project</a>!]</td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">team_id</td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>teamUsers</strong></td>
<td valign="top">[<a href="#user">User</a>!]</td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">team_id</td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>user</strong></td>
<td valign="top"><a href="#user">User</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">id</td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>users</strong></td>
<td valign="top"><a href="#userpaginator">UserPaginator</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">count</td>
<td valign="top"><a href="#int">Int</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">page</td>
<td valign="top"><a href="#int">Int</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>userTeams</strong></td>
<td valign="top">[<a href="#team">Team</a>!]</td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">user_id</td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>node</strong></td>
<td valign="top"><a href="#node">Node</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">id</td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
</tbody>
</table>

## Mutation
<table>
<thead>
<tr>
<th align="left">Field</th>
<th align="right">Argument</th>
<th align="left">Type</th>
<th align="left">Description</th>
</tr>
</thead>
<tbody>
<tr>
<td colspan="2" valign="top"><strong>createTeam</strong></td>
<td valign="top"><a href="#team">Team</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">team</td>
<td valign="top"><a href="#teaminput">TeamInput</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>updateTeam</strong></td>
<td valign="top"><a href="#team">Team</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">id</td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">team</td>
<td valign="top"><a href="#teaminput">TeamInput</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>deleteTeam</strong></td>
<td valign="top"><a href="#team">Team</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">id</td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>createUser</strong></td>
<td valign="top"><a href="#user">User</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">user</td>
<td valign="top"><a href="#userinput">UserInput</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>updateUser</strong></td>
<td valign="top"><a href="#user">User</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">id</td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">user</td>
<td valign="top"><a href="#userinput">UserInput</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>deleteUser</strong></td>
<td valign="top"><a href="#user">User</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">id</td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>addUserRole</strong></td>
<td valign="top"><a href="#user">User</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">id</td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">role</td>
<td valign="top"><a href="#string">String</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">team_id</td>
<td valign="top"><a href="#int">Int</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>removeUserRole</strong></td>
<td valign="top"><a href="#user">User</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">id</td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">role</td>
<td valign="top"><a href="#string">String</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">team_id</td>
<td valign="top"><a href="#int">Int</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>createProject</strong></td>
<td valign="top"><a href="#project">Project</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">project</td>
<td valign="top"><a href="#createprojectinput">CreateProjectInput</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>updateProject</strong></td>
<td valign="top"><a href="#project">Project</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">id</td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">project</td>
<td valign="top"><a href="#updateprojectinput">UpdateProjectInput</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>deleteProject</strong></td>
<td valign="top"><a href="#project">Project</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">id</td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>buildProject</strong></td>
<td valign="top"><a href="#build">Build</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">id</td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" align="right" valign="top">commit</td>
<td valign="top"><a href="#string">String</a>!</td>
<td></td>
</tr>
</tbody>
</table>

## Objects

### Build

<table>
<thead>
<tr>
<th align="left">Field</th>
<th align="right">Argument</th>
<th align="left">Type</th>
<th align="left">Description</th>
</tr>
</thead>
<tbody>
<tr>
<td colspan="2" valign="top"><strong>id</strong></td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>project</strong></td>
<td valign="top"><a href="#project">Project</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>user</strong></td>
<td valign="top"><a href="#user">User</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>status</strong></td>
<td valign="top"><a href="#string">String</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>commit</strong></td>
<td valign="top"><a href="#string">String</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>output</strong></td>
<td valign="top"><a href="#string">String</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>completed_at</strong></td>
<td valign="top"><a href="#datetime">DateTime</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>created_at</strong></td>
<td valign="top"><a href="#datetime">DateTime</a>!</td>
<td></td>
</tr>
</tbody>
</table>

### BuildPaginator

<table>
<thead>
<tr>
<th align="left">Field</th>
<th align="right">Argument</th>
<th align="left">Type</th>
<th align="left">Description</th>
</tr>
</thead>
<tbody>
<tr>
<td colspan="2" valign="top"><strong>paginatorInfo</strong></td>
<td valign="top"><a href="#paginatorinfo">PaginatorInfo</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>data</strong></td>
<td valign="top">[<a href="#build">Build</a>!]!</td>
<td></td>
</tr>
</tbody>
</table>

### PageInfo

<table>
<thead>
<tr>
<th align="left">Field</th>
<th align="right">Argument</th>
<th align="left">Type</th>
<th align="left">Description</th>
</tr>
</thead>
<tbody>
<tr>
<td colspan="2" valign="top"><strong>hasNextPage</strong></td>
<td valign="top"><a href="#boolean">Boolean</a>!</td>
<td>

When paginating forwards, are there more items?

</td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>hasPreviousPage</strong></td>
<td valign="top"><a href="#boolean">Boolean</a>!</td>
<td>

When paginating backwards, are there more items?

</td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>startCursor</strong></td>
<td valign="top"><a href="#string">String</a></td>
<td>

When paginating backwards, the cursor to continue.

</td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>endCursor</strong></td>
<td valign="top"><a href="#string">String</a></td>
<td>

When paginating forwards, the cursor to continue.

</td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>total</strong></td>
<td valign="top"><a href="#int">Int</a></td>
<td>

Total number of node in connection.

</td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>count</strong></td>
<td valign="top"><a href="#int">Int</a></td>
<td>

Count of nodes in current request.

</td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>currentPage</strong></td>
<td valign="top"><a href="#int">Int</a></td>
<td>

Current page of request.

</td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>lastPage</strong></td>
<td valign="top"><a href="#int">Int</a></td>
<td>

Last page in connection.

</td>
</tr>
</tbody>
</table>

### PaginatorInfo

<table>
<thead>
<tr>
<th align="left">Field</th>
<th align="right">Argument</th>
<th align="left">Type</th>
<th align="left">Description</th>
</tr>
</thead>
<tbody>
<tr>
<td colspan="2" valign="top"><strong>count</strong></td>
<td valign="top"><a href="#int">Int</a>!</td>
<td>

Total count of available items in the page.

</td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>currentPage</strong></td>
<td valign="top"><a href="#int">Int</a>!</td>
<td>

Current pagination page.

</td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>firstItem</strong></td>
<td valign="top"><a href="#int">Int</a></td>
<td>

Index of first item in the current page.

</td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>hasMorePages</strong></td>
<td valign="top"><a href="#boolean">Boolean</a>!</td>
<td>

If collection has more pages.

</td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>lastItem</strong></td>
<td valign="top"><a href="#int">Int</a></td>
<td>

Index of last item in the current page.

</td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>lastPage</strong></td>
<td valign="top"><a href="#int">Int</a>!</td>
<td>

Last page number of the collection.

</td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>perPage</strong></td>
<td valign="top"><a href="#int">Int</a>!</td>
<td>

Number of items per page in the collection.

</td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>total</strong></td>
<td valign="top"><a href="#int">Int</a>!</td>
<td>

Total items available in the collection.

</td>
</tr>
</tbody>
</table>

### Project

<table>
<thead>
<tr>
<th align="left">Field</th>
<th align="right">Argument</th>
<th align="left">Type</th>
<th align="left">Description</th>
</tr>
</thead>
<tbody>
<tr>
<td colspan="2" valign="top"><strong>id</strong></td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>team</strong></td>
<td valign="top"><a href="#team">Team</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>name</strong></td>
<td valign="top"><a href="#string">String</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>repository</strong></td>
<td valign="top"><a href="#string">String</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>created_at</strong></td>
<td valign="top"><a href="#datetime">DateTime</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>updated_at</strong></td>
<td valign="top"><a href="#datetime">DateTime</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>latestBuild</strong></td>
<td valign="top"><a href="#build">Build</a></td>
<td></td>
</tr>
</tbody>
</table>

### ProjectPaginator

<table>
<thead>
<tr>
<th align="left">Field</th>
<th align="right">Argument</th>
<th align="left">Type</th>
<th align="left">Description</th>
</tr>
</thead>
<tbody>
<tr>
<td colspan="2" valign="top"><strong>paginatorInfo</strong></td>
<td valign="top"><a href="#paginatorinfo">PaginatorInfo</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>data</strong></td>
<td valign="top">[<a href="#project">Project</a>!]!</td>
<td></td>
</tr>
</tbody>
</table>

### Team

<table>
<thead>
<tr>
<th align="left">Field</th>
<th align="right">Argument</th>
<th align="left">Type</th>
<th align="left">Description</th>
</tr>
</thead>
<tbody>
<tr>
<td colspan="2" valign="top"><strong>id</strong></td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>name</strong></td>
<td valign="top"><a href="#string">String</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>created_at</strong></td>
<td valign="top"><a href="#datetime">DateTime</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>updated_at</strong></td>
<td valign="top"><a href="#datetime">DateTime</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>users</strong></td>
<td valign="top">[<a href="#user">User</a>!]</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>projects</strong></td>
<td valign="top">[<a href="#project">Project</a>!]</td>
<td></td>
</tr>
</tbody>
</table>

### TeamPaginator

<table>
<thead>
<tr>
<th align="left">Field</th>
<th align="right">Argument</th>
<th align="left">Type</th>
<th align="left">Description</th>
</tr>
</thead>
<tbody>
<tr>
<td colspan="2" valign="top"><strong>paginatorInfo</strong></td>
<td valign="top"><a href="#paginatorinfo">PaginatorInfo</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>data</strong></td>
<td valign="top">[<a href="#team">Team</a>!]!</td>
<td></td>
</tr>
</tbody>
</table>

### User

<table>
<thead>
<tr>
<th align="left">Field</th>
<th align="right">Argument</th>
<th align="left">Type</th>
<th align="left">Description</th>
</tr>
</thead>
<tbody>
<tr>
<td colspan="2" valign="top"><strong>id</strong></td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>name</strong></td>
<td valign="top"><a href="#string">String</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>email</strong></td>
<td valign="top"><a href="#string">String</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>created_at</strong></td>
<td valign="top"><a href="#datetime">DateTime</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>updated_at</strong></td>
<td valign="top"><a href="#datetime">DateTime</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>userRoles</strong></td>
<td valign="top">[<a href="#userrole">UserRole</a>!]</td>
<td></td>
</tr>
</tbody>
</table>

### UserPaginator

<table>
<thead>
<tr>
<th align="left">Field</th>
<th align="right">Argument</th>
<th align="left">Type</th>
<th align="left">Description</th>
</tr>
</thead>
<tbody>
<tr>
<td colspan="2" valign="top"><strong>paginatorInfo</strong></td>
<td valign="top"><a href="#paginatorinfo">PaginatorInfo</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>data</strong></td>
<td valign="top">[<a href="#user">User</a>!]!</td>
<td></td>
</tr>
</tbody>
</table>

### UserRole

<table>
<thead>
<tr>
<th align="left">Field</th>
<th align="right">Argument</th>
<th align="left">Type</th>
<th align="left">Description</th>
</tr>
</thead>
<tbody>
<tr>
<td colspan="2" valign="top"><strong>id</strong></td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>user</strong></td>
<td valign="top"><a href="#user">User</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>role</strong></td>
<td valign="top"><a href="#string">String</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>team</strong></td>
<td valign="top"><a href="#team">Team</a></td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>created_at</strong></td>
<td valign="top"><a href="#datetime">DateTime</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>updated_at</strong></td>
<td valign="top"><a href="#datetime">DateTime</a></td>
<td></td>
</tr>
</tbody>
</table>

## Inputs

### CreateProjectInput

<table>
<thead>
<tr>
<th colspan="2" align="left">Field</th>
<th align="left">Type</th>
<th align="left">Description</th>
</tr>
</thead>
<tbody>
<tr>
<td colspan="2" valign="top"><strong>team_id</strong></td>
<td valign="top"><a href="#id">ID</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>name</strong></td>
<td valign="top"><a href="#string">String</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>repository</strong></td>
<td valign="top"><a href="#string">String</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>private_key</strong></td>
<td valign="top"><a href="#string">String</a>!</td>
<td></td>
</tr>
</tbody>
</table>

### TeamInput

<table>
<thead>
<tr>
<th colspan="2" align="left">Field</th>
<th align="left">Type</th>
<th align="left">Description</th>
</tr>
</thead>
<tbody>
<tr>
<td colspan="2" valign="top"><strong>name</strong></td>
<td valign="top"><a href="#string">String</a>!</td>
<td></td>
</tr>
</tbody>
</table>

### UpdateProjectInput

<table>
<thead>
<tr>
<th colspan="2" align="left">Field</th>
<th align="left">Type</th>
<th align="left">Description</th>
</tr>
</thead>
<tbody>
<tr>
<td colspan="2" valign="top"><strong>name</strong></td>
<td valign="top"><a href="#string">String</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>repository</strong></td>
<td valign="top"><a href="#string">String</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>private_key</strong></td>
<td valign="top"><a href="#string">String</a>!</td>
<td></td>
</tr>
</tbody>
</table>

### UserInput

<table>
<thead>
<tr>
<th colspan="2" align="left">Field</th>
<th align="left">Type</th>
<th align="left">Description</th>
</tr>
</thead>
<tbody>
<tr>
<td colspan="2" valign="top"><strong>name</strong></td>
<td valign="top"><a href="#string">String</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>email</strong></td>
<td valign="top"><a href="#string">String</a>!</td>
<td></td>
</tr>
<tr>
<td colspan="2" valign="top"><strong>password</strong></td>
<td valign="top"><a href="#string">String</a>!</td>
<td></td>
</tr>
</tbody>
</table>

## Scalars

### Boolean

The `Boolean` scalar type represents `true` or `false`.

### Date

### DateTime

### Float

The `Float` scalar type represents signed double-precision fractional
values as specified by
[IEEE 754](http://en.wikipedia.org/wiki/IEEE_floating_point). 

### ID

The `ID` scalar type represents a unique identifier, often used to
refetch an object or as key for a cache. The ID type appears in a JSON
response as a String; however, it is not intended to be human-readable.
When expected as an input type, any string (such as `"4"`) or integer
(such as `4`) input value will be accepted as an ID.

### Int

The `Int` scalar type represents non-fractional signed whole numeric
values. Int can represent values between -(2^31) and 2^31 - 1. 

### String

The `String` scalar type represents textual data, represented as UTF-8
character sequences. The String type is most often used by GraphQL to
represent free-form human-readable text.


## Interfaces


### Node

Node global interface

<table>
<thead>
<tr>
<th align="left">Field</th>
<th align="right">Argument</th>
<th align="left">Type</th>
<th align="left">Description</th>
</tr>
</thead>
<tbody>
<tr>
<td colspan="2" valign="top"><strong>id</strong></td>
<td valign="top"><a href="#id">ID</a>!</td>
<td>

Global identifier that can be used to resolve any Node implementation.

</td>
</tr>
</tbody>
</table>
