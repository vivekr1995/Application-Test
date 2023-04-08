import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { forkJoin, Observable, throwError, map, retry, catchError } from 'rxjs';
import { OrderData } from '../interface/orderData';
import { environment } from 'src/environments/environment';
import { MatSnackBar } from '@angular/material/snack-bar';

@Injectable({
  providedIn: 'root',
})
export class OrderService {
  
  /**
   * Getting data from environment
   */
  apiUrl = environment.apiUrl;

  /**
   * Creates instance
   * @param http 
   */
  constructor(private http: HttpClient, public snackBar : MatSnackBar) {}
  
  /**
   * Gets csv data - To get all existing data from CSV
   * @returns csv data 
   */
  getOrders(): Observable<OrderData[]> {
    return this.http
      .get(this.apiUrl)
      .pipe<OrderData[]>(map((data: any) => data.data))
      .pipe(retry(1), catchError(this.handleError));
  }

  /**
   * Adds new entry in CSV
   * @param order 
   * @returns OrderData 
   */
  addOrder(order: OrderData): Observable<OrderData> {
    return this.http.post<OrderData>(`${this.apiUrl}`, order);
  }

  /**
   * To Update single data row
   * @param order 
   * @returns OrderData 
   */
  updateOrder(order: OrderData): Observable<OrderData> {
    return this.http.patch<OrderData>(`${this.apiUrl}`, order);
  }

  /**
   * To delete single entry from CSV
   * @param id 
   * @returns OrderData 
   */
  deleteOrder(id: number): Observable<OrderData> {
    return this.http.delete<OrderData>(`${this.apiUrl}${id}`);
  }

  /**
   * To delete multiple selected data from CSV
   * @param id 
   * @returns OrderData 
   */
  deleteOrders(order: OrderData[]): Observable<OrderData[]> {
    return forkJoin(
      order.map((order) =>
        this.http.delete<OrderData>(`${this.apiUrl}${order.id}`)
      )
    );
  }

  /**
   * Exception handler
   * @param error
   * @returns Error message 
   */
  handleError(error:any) {
    let errorMessage = '';
    if (error.error instanceof ErrorEvent) {
      // client-side error
      errorMessage = `Error: ${error.error.message}`;
    } else {
      // server-side error
      errorMessage = `Error Code: ${error.status}\nMessage: ${error.message}`;
    }
    return throwError(() => {
        return errorMessage;
    });
  }

  /**
   * Shows success message after add, delete update actions
   * @param message
   * @param title
   * @return {void} returns nothing
   */
  showSuccessMessage(message : string, title : string) {
    this.snackBar.open(message, title, {
      duration : 2000,
      panelClass : ['mat-toolbar', 'mat-success']
    });
  }

  /**
   * Shows error message after add, delete update actions
   * @param message
   * @param title
   * @return {void} returns nothing
   */
  showErrorMessage(message : string, title : string) {
    this.snackBar.open(message, title, {
      duration : 2000,
      panelClass : ['mat-toolbar', 'mat-warn']
    });
  }
}
