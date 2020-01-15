import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class AanwezigheidService {
  constructor(private httpClient: HttpClient) {}

  UpdateCoachAanwezigheid(matchId: number, isAanwezig: string) {
    this.httpClient
      .post(
        environment.baseUrl,
        {
          matchId,
          isAanwezig
        },
        {
          params: { action: 'UpdateCoachAanwezigheid' }
        }
      )
      .subscribe();
  }

  GetCoachAanwezigheid() {
    return this.httpClient.get<any>(environment.baseUrl, {
      params: {
        action: 'GetCoachAanwezigheid'
      }
    });
  }

  UpdateAanwezigheid(
    matchId: number,
    isAanwezig: boolean,
    spelerId: string,
    rol: string
  ) {
    this.httpClient
      .post<any>(environment.baseUrl + 'aanwezigheid', {
        matchId,
        spelerId,
        isAanwezig,
        rol
      })
      .subscribe();
  }

  GetWedstrijdAanwezigheid() {
    return this.httpClient.get<any[]>(environment.baseUrl, {
      params: {
        action: 'GetWedstrijdAanwezigheid'
      }
    });
  }

  DeleteBarcieAanwezigheid(date: string, shift: string, barcielidId: string) {
    return this.httpClient.post<any>(
      environment.baseUrl + 'barcie/dienst/delete',
      {
        date,
        barcielidId,
        shift
      }
    );
  }

  AddBarcieAanwezigheid(date: string, shift: number, barcielidId: string) {
    return this.httpClient.post<any>(
      environment.baseUrl + 'barcie/dienst/add',
      {
        date,
        shift,
        barcielidId
      }
    );
  }
}
