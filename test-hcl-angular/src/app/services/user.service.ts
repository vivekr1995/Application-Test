import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { forkJoin, Observable } from 'rxjs';
import { User } from '../model/user';
import { map } from 'rxjs/operators';

@Injectable({
  providedIn: 'root',
})
export class UserService {
  private getDataUrl = 'http://localhost/test-hcl-php/getData';
  private updateDataUrl = 'http://localhost/test-hcl-php/updateData';
  private addDataUrl = 'http://localhost/test-hcl-php/addData';
  private deleteDataUrl = 'http://localhost/test-hcl-php/deleteData';

  constructor(private http: HttpClient) {}

  getUsers(): Observable<User[]> {
    return this.http
      .get(this.getDataUrl)
      .pipe<User[]>(map((data: any) => data.users));
  }

  updateUser(user: User): Observable<User> {
    return this.http.patch<User>(`${this.updateDataUrl}/${user.id}`, user);
  }

  addUser(user: User): Observable<User> {
    return this.http.post<User>(`${this.addDataUrl}`, user);
  }

  deleteUser(id: number): Observable<User> {
    return this.http.delete<User>(`${this.deleteDataUrl}/${id}`);
  }

  deleteUsers(users: User[]): Observable<User[]> {
    return forkJoin(
      users.map((user) =>
        this.http.delete<User>(`${this.deleteDataUrl}/${user.id}`)
      )
    );
  }
}
